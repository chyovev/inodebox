import { useEffect, useState, type FormEvent, type ReactNode } from 'react';

export type InodeEntry = {
    id: number;
    name: string;
};

export type InodeContents = {
    name: string;
    directories: InodeEntry[];
    files: InodeEntry[];
};

type CreateKind = 'file' | 'directory';

type CreateTarget = {
    parentId: number;
    kind: CreateKind;
};

type Props = {
    /** Inode to list on first render. Defaults to the root directory (1). */
    initialId?: number;
};

const ROOT_ID = 1;

const CREATE_ENDPOINTS: Record<CreateKind, string> = {
    file: 'files',
    directory: 'directories',
};

const CREATE_TITLES: Record<CreateKind, string> = {
    file: 'New file',
    directory: 'New directory',
};

const ICON_CLASS = 'h-4 w-4';

function FileIcon() {
    return (
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" className={ICON_CLASS}>
            <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" />
            <path d="M14 3v6h6" />
            <path d="M12 12v6M9 15h6" />
        </svg>
    );
}

function FolderIcon() {
    return (
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" className={ICON_CLASS}>
            <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <path d="M12 11v5M9.5 13.5h5" />
        </svg>
    );
}

const ICONS: Record<CreateKind, () => ReactNode> = {
    file: FileIcon,
    directory: FolderIcon,
};

export default function InodeTable({ initialId = ROOT_ID }: Props) {
    const [id, setId] = useState<number>(initialId);
    const [history, setHistory] = useState<number[]>([]);
    const [data, setData] = useState<InodeContents | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [reloadKey, setReloadKey] = useState<number>(0);

    const [createTarget, setCreateTarget] = useState<CreateTarget | null>(null);
    const [name, setName] = useState<string>('');
    const [submitting, setSubmitting] = useState<boolean>(false);
    const [createError, setCreateError] = useState<string | null>(null);

    useEffect(() => {
        const controller = new AbortController();

        setLoading(true);
        setError(null);

        fetch(`/api/inodes/${id}`, {
            headers: { Accept: 'application/json' },
            signal: controller.signal,
        })
            .then(async (response) => {
                if (!response.ok) {
                    throw new Error(`Request failed with status ${response.status}`);
                }

                return (await response.json()) as InodeContents;
            })
            .then((json) => setData(json))
            .catch((err: unknown) => {
                if (err instanceof DOMException && err.name === 'AbortError') {
                    return;
                }

                setData(null);
                setError(err instanceof Error ? err.message : 'Unknown error');
            })
            .finally(() => {
                if (!controller.signal.aborted) {
                    setLoading(false);
                }
            });

        return () => controller.abort();
    }, [id, reloadKey]);

    // Close the dialog on Escape
    useEffect(() => {
        if (!createTarget) {
            return;
        }

        const onKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape' && !submitting) {
                closeDialog();
            }
        };

        document.addEventListener('keydown', onKeyDown);

        return () => document.removeEventListener('keydown', onKeyDown);
    }, [createTarget, submitting]);

    const openDirectory = (targetId: number) => {
        setHistory((prev) => [...prev, id]);
        setId(targetId);
    };

    const goBack = () => {
        const previous = history[history.length - 1];

        if (previous === undefined) {
            return;
        }

        setHistory(history.slice(0, -1));
        setId(previous);
    };

    const openCreateDialog = (parentId: number, kind: CreateKind) => {
        setName('');
        setCreateError(null);
        setCreateTarget({ parentId, kind });
    };

    const closeDialog = () => {
        setCreateTarget(null);
        setCreateError(null);
    };

    const createItem = async (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        if (!createTarget) {
            return;
        }

        setSubmitting(true);
        setCreateError(null);

        try {
            const response = await fetch(
                `/api/inodes/${createTarget.parentId}/${CREATE_ENDPOINTS[createTarget.kind]}`,
                {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name }),
                },
            );

            if (!response.ok) {
                let message = `Request failed with status ${response.status}`;

                try {
                    const body = (await response.json()) as { error?: string };
                    if (body.error) {
                        message = body.error;
                    }
                } catch {
                    // response had no JSON body, keep the generic message
                }

                throw new Error(message);
            }

            setCreateTarget(null);
            setName('');
            setReloadKey((key) => key + 1);
        } catch (err) {
            setCreateError(err instanceof Error ? err.message : 'Unknown error');
        } finally {
            setSubmitting(false);
        }
    };

    const addButtons = (parentId: number, disabled: boolean) => (
        <div className="inline-flex items-center gap-1.5">
            {(Object.keys(CREATE_TITLES) as CreateKind[]).map((kind) => {
                const Icon = ICONS[kind];
                return (
                    <button
                        key={kind}
                        type="button"
                        onClick={() => openCreateDialog(parentId, kind)}
                        disabled={disabled}
                        aria-label={CREATE_TITLES[kind]}
                        title={CREATE_TITLES[kind]}
                        className="flex h-7 w-7 items-center justify-center rounded-sm border border-[#e3e3e0] hover:bg-white disabled:cursor-not-allowed disabled:opacity-40 dark:border-[#3E3E3A] dark:hover:bg-[#161615]"
                    >
                        <Icon />
                    </button>
                );
            })}
        </div>
    );

    const rows = data
        ? [
              ...data.directories.map((entry) => ({ ...entry, type: 'Directory' as const })),
              ...data.files.map((entry) => ({ ...entry, type: 'File' as const })),
          ]
        : [];

    return (
        <div className="mx-auto w-full max-w-3xl p-4 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
            <div className="mb-3 flex items-center justify-between gap-2">
                <div className="flex items-center gap-2">
                    <button
                        type="button"
                        onClick={goBack}
                        disabled={history.length === 0 || loading}
                        className="rounded-sm border border-[#e3e3e0] px-3 py-1 hover:bg-[#f5f5f3] disabled:cursor-not-allowed disabled:opacity-40 dark:border-[#3E3E3A] dark:hover:bg-[#1f1f1d]"
                    >
                        Back
                    </button>
                    <h2 className="font-medium">{data?.name ?? '…'}</h2>
                </div>
                {loading && <span className="text-[#706f6c] dark:text-[#A1A09A]">Loading…</span>}
            </div>

            {error && (
                <div role="alert" className="mb-3 rounded-sm border border-red-300 bg-red-50 p-3 text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
                    Could not load folder {id}: {error}
                </div>
            )}

            <div className="overflow-x-auto rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <table className="w-full border-collapse text-left">
                    <thead className="bg-[#f5f5f3] dark:bg-[#1f1f1d]">
                        <tr>
                            <th className="px-3 py-2 font-medium">Name</th>
                            <th className="w-32 px-3 py-2 font-medium">Type</th>
                            <th className="px-3 py-2 text-right font-medium">{addButtons(id, !data)}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {rows.length === 0 && !loading && data && (
                            <tr>
                                <td colSpan={3} className="px-3 py-4 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                    This folder is empty.
                                </td>
                            </tr>
                        )}
                        {rows.map((row) => (
                            <tr key={`${row.type}-${row.id}`} className="border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                                <td className="px-3 py-2">
                                    {row.type === 'Directory' ? (
                                        <button
                                            type="button"
                                            onClick={() => openDirectory(row.id)}
                                            className="font-medium text-[#f53003] hover:underline dark:text-[#FF4433]"
                                        >
                                            {row.name}
                                        </button>
                                    ) : (
                                        <span>{row.name}</span>
                                    )}
                                </td>
                                <td className="px-3 py-2 text-[#706f6c] dark:text-[#A1A09A]">{row.type}</td>
                                <td className="px-3 py-2 text-right">{addButtons(row.id, false)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {createTarget && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                    onMouseDown={(event) => {
                        if (event.target === event.currentTarget && !submitting) {
                            closeDialog();
                        }
                    }}
                >
                    <form
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="create-dialog-title"
                        onSubmit={createItem}
                        className="w-full max-w-sm rounded-sm border border-[#e3e3e0] bg-white p-5 shadow-lg dark:border-[#3E3E3A] dark:bg-[#161615]"
                    >
                        <h3 id="create-dialog-title" className="mb-3 font-medium">
                            {CREATE_TITLES[createTarget.kind]}
                        </h3>

                        <label className="mb-1 block text-[#706f6c] dark:text-[#A1A09A]" htmlFor="create-name">
                            Name
                        </label>
                        <input
                            id="create-name"
                            type="text"
                            autoFocus
                            value={name}
                            onChange={(event) => setName(event.target.value)}
                            disabled={submitting}
                            placeholder={createTarget.kind === 'file' ? 'document.txt' : 'myfolder'}
                            className="mb-1 w-full rounded-sm border border-[#e3e3e0] bg-transparent px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#f53003]/40 dark:border-[#3E3E3A]"
                        />
                        <p className="mb-3 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                            Letters and numbers only{createTarget.kind === 'file' ? ', with an optional lowercase extension' : ''}.
                        </p>

                        {createError && (
                            <div role="alert" className="mb-3 rounded-sm border border-red-300 bg-red-50 p-2 text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
                                {createError}
                            </div>
                        )}

                        <div className="flex justify-end gap-2">
                            <button
                                type="button"
                                onClick={closeDialog}
                                disabled={submitting}
                                className="rounded-sm border border-[#e3e3e0] px-3 py-1.5 hover:bg-[#f5f5f3] disabled:opacity-40 dark:border-[#3E3E3A] dark:hover:bg-[#1f1f1d]"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={submitting || name.trim() === ''}
                                className="rounded-sm border border-black bg-[#1b1b18] px-3 py-1.5 text-white hover:bg-black disabled:cursor-not-allowed disabled:opacity-40 dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                            >
                                {submitting ? 'Creating…' : 'Create'}
                            </button>
                        </div>
                    </form>
                </div>
            )}
        </div>
    );
}
