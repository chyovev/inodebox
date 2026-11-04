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

type FilteredInode = {
    parents: InodeEntry[];
    parent_id: number;
    name: string;
};

type CreateKind = 'file' | 'directory';

type CreateTarget = {
    parentId: number;
    kind: CreateKind;
};

type Toast = {
    kind: 'success' | 'error';
    message: string;
};

type Props = {
    /** Inode to list on first render. Defaults to the root directory (1). */
    initialId?: number;
};

const ROOT_ID = 1;
const TOAST_DURATION_MS = 4000;

const CREATE_ENDPOINTS: Record<CreateKind, string> = {
    file: 'files',
    directory: 'directories',
};

const CREATE_TITLES: Record<CreateKind, string> = {
    file: 'New file',
    directory: 'New directory',
};

// ---------------------------------------------------------------------------
// Request tracking: every backend call goes through apiFetch, which keeps a
// count of in-flight requests so the preloader can be shown while any is pending.
// ---------------------------------------------------------------------------

let pendingRequests = 0;
const pendingListeners = new Set<() => void>();

function changePendingRequests(delta: number): void {
    pendingRequests += delta;
    pendingListeners.forEach((listener) => listener());
}

function apiFetch(input: string, init?: RequestInit): Promise<Response> {
    changePendingRequests(1);

    // hidden once the response (or a network error) arrives
    return fetch(input, init).finally(() => changePendingRequests(-1));
}

function usePendingRequests(): number {
    const [count, setCount] = useState<number>(pendingRequests);

    useEffect(() => {
        const listener = () => setCount(pendingRequests);

        pendingListeners.add(listener);
        listener();

        return () => {
            pendingListeners.delete(listener);
        };
    }, []);

    return count;
}

function Preloader() {
    const pending = usePendingRequests();

    if (pending === 0) {
        return null;
    }

    return (
        <div
            role="status"
            aria-live="polite"
            aria-label="Loading"
            className="fixed inset-0 z-[60] flex items-center justify-center bg-black/40"
        >
            <div className="h-12 w-12 animate-spin rounded-full border-4 border-white/30 border-t-white" />
        </div>
    );
}

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

function TrashIcon() {
    return (
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" className={ICON_CLASS}>
            <path d="M3 6h18" />
            <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" />
            <path d="M6 6l1 14a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-14" />
            <path d="M10 11v6M14 11v6" />
        </svg>
    );
}

const CREATE_ICONS: Record<CreateKind, () => ReactNode> = {
    file: FileIcon,
    directory: FolderIcon,
};

const iconButtonClass =
    'flex h-7 w-7 items-center justify-center rounded-sm border border-[#e3e3e0] hover:bg-white disabled:cursor-not-allowed disabled:opacity-40 dark:border-[#3E3E3A] dark:hover:bg-[#161615]';

export default function InodeTable({ initialId = ROOT_ID }: Props) {
    const [id, setId] = useState<number>(initialId);
    const [history, setHistory] = useState<number[]>([]);
    const [data, setData] = useState<InodeContents | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [reloadKey, setReloadKey] = useState<number>(0);

    const [toast, setToast] = useState<Toast | null>(null);
    const [deletingId, setDeletingId] = useState<number | null>(null);

    const [createTarget, setCreateTarget] = useState<CreateTarget | null>(null);
    const [name, setName] = useState<string>('');
    const [submitting, setSubmitting] = useState<boolean>(false);
    const [createError, setCreateError] = useState<string | null>(null);

    useEffect(() => {
        const controller = new AbortController();

        setLoading(true);
        setError(null);

        apiFetch(`/api/inodes/${id}`, {
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

    // Auto-dismiss toasts
    useEffect(() => {
        if (!toast) {
            return;
        }

        const timer = window.setTimeout(() => setToast(null), TOAST_DURATION_MS);

        return () => window.clearTimeout(timer);
    }, [toast]);

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
            const response = await apiFetch(
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
                throw new Error(await readErrorMessage(response));
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

    // Deletes an inode and reports the outcome with a toast. If the deleted
    // inode is the folder being viewed, the user is returned to its parent:
    // the previous folder in the navigation history, or the root when the
    // view was opened directly.
    const deleteInode = async (target: { id: number; name: string }, isCurrent: boolean) => {
        const message = isCurrent
            ? `Delete the folder "${target.name}" and everything in it?`
            : `Delete "${target.name}"?`;

        if (!window.confirm(message)) {
            return;
        }

        setDeletingId(target.id);

        try {
            const response = await apiFetch(`/api/inodes/${target.id}`, {
                method: 'DELETE',
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(await readErrorMessage(response));
            }

            setToast({ kind: 'success', message: `Deleted "${target.name}".` });

            if (isCurrent) {
                const parent = history[history.length - 1];
                setHistory(parent === undefined ? [] : history.slice(0, -1));
                setId(parent ?? ROOT_ID);
            } else {
                setReloadKey((key) => key + 1);
            }
        } catch (err) {
            setToast({
                kind: 'error',
                message: `Could not delete "${target.name}": ${err instanceof Error ? err.message : 'Unknown error'}`,
            });
        } finally {
            setDeletingId(null);
        }
    };

    const addButtons = (parentId: number, disabled: boolean) => (
        <div className="inline-flex items-center gap-1.5">
            {(Object.keys(CREATE_TITLES) as CreateKind[]).map((kind) => {
                const Icon = CREATE_ICONS[kind];
                return (
                    <button
                        key={kind}
                        type="button"
                        onClick={() => openCreateDialog(parentId, kind)}
                        disabled={disabled}
                        aria-label={CREATE_TITLES[kind]}
                        title={CREATE_TITLES[kind]}
                        className={iconButtonClass}
                    >
                        <Icon />
                    </button>
                );
            })}
        </div>
    );

    const deleteButton = (target: { id: number; name: string }, isCurrent: boolean, disabled: boolean) => (
        <button
            type="button"
            onClick={() => deleteInode(target, isCurrent)}
            disabled={disabled || deletingId !== null}
            aria-label={isCurrent ? 'Delete this folder' : `Delete ${target.name}`}
            title={isCurrent ? 'Delete this folder' : 'Delete'}
            className={`${iconButtonClass} text-red-700 dark:text-red-400`}
        >
            <TrashIcon />
        </button>
    );

    const rows = data
        ? [
              ...data.directories.map((entry) => ({ ...entry, type: 'Directory' as const })),
              ...data.files.map((entry) => ({ ...entry, type: 'File' as const })),
          ]
        : [];

    return (
        <div className="mx-auto w-full max-w-3xl p-4 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
            <Preloader />
            <h1 className="mb-4 text-5xl font-bold tracking-tight">Inodebox</h1>

            <InodeSearch onSelect={openDirectory} />

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
                            <th className="px-3 py-2 text-right font-medium">
                                <div className="inline-flex items-center gap-1.5">
                                    {addButtons(id, !data)}
                                    {data && deleteButton({ id, name: data.name }, true, false)}
                                </div>
                            </th>
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
                                <td className="px-3 py-2 text-right">
                                    <div className="inline-flex items-center gap-1.5">
                                        {addButtons(row.id, false)}
                                        {deleteButton(row, false, false)}
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {toast && (
                <div
                    role={toast.kind === 'error' ? 'alert' : 'status'}
                    aria-live={toast.kind === 'error' ? 'assertive' : 'polite'}
                    className={`fixed right-4 bottom-4 z-50 flex max-w-sm items-start gap-3 rounded-sm border px-4 py-3 shadow-lg ${
                        toast.kind === 'error'
                            ? 'border-red-300 bg-red-600 text-white dark:border-red-800'
                            : 'border-green-300 bg-green-600 text-white dark:border-green-800'
                    }`}
                >
                    <span className="flex-1">{toast.message}</span>
                    <button
                        type="button"
                        onClick={() => setToast(null)}
                        aria-label="Dismiss"
                        className="leading-none opacity-80 hover:opacity-100"
                    >
                        ×
                    </button>
                </div>
            )}

            {createTarget && (
                <div
                    className="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-4"
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

async function readErrorMessage(response: Response): Promise<string> {
    let message = `Request failed with status ${response.status}`;

    try {
        const body = (await response.json()) as { error?: string };
        if (body.error) {
            message = body.error;
        }
    } catch {
        // response had no JSON body, keep the generic message
    }

    return message;
}

const MIN_SEARCH_LENGTH = 3;
const SEARCH_DEBOUNCE_MS = 300;

// Route-level search: calls the filter endpoint on the root folder
type InodeSearchProps = {
    /** Called with the ID of the directory that contains the selected result */
    onSelect: (parentId: number) => void;
};

function InodeSearch({ onSelect }: InodeSearchProps) {
    const [query, setQuery] = useState<string>('');
    const [results, setResults] = useState<FilteredInode[] | null>(null);
    const [searching, setSearching] = useState<boolean>(false);
    const [searchError, setSearchError] = useState<string | null>(null);

    const term = query.trim();

    useEffect(() => {
        if (term.length < MIN_SEARCH_LENGTH) {
            setResults(null);
            setSearchError(null);
            setSearching(false);
            return;
        }

        const controller = new AbortController();

        setSearching(true);
        setSearchError(null);

        const timer = window.setTimeout(() => {
            apiFetch(`/api/inodes/${ROOT_ID}/files/filter?${new URLSearchParams({ search: term })}`, {
                headers: { Accept: 'application/json' },
                signal: controller.signal,
            })
                .then(async (response) => {
                    if (!response.ok) {
                        throw new Error(await readErrorMessage(response));
                    }

                    return (await response.json()) as FilteredInode[];
                })
                .then((json) => setResults(json))
                .catch((err: unknown) => {
                    if (err instanceof DOMException && err.name === 'AbortError') {
                        return;
                    }

                    setResults(null);
                    setSearchError(err instanceof Error ? err.message : 'Unknown error');
                })
                .finally(() => {
                    if (!controller.signal.aborted) {
                        setSearching(false);
                    }
                });
        }, SEARCH_DEBOUNCE_MS);

        return () => {
            window.clearTimeout(timer);
            controller.abort();
        };
    }, [term]);

    return (
        <section className="relative mb-6">
            <label htmlFor="inode-search" className="sr-only">
                Search files
            </label>
            <input
                id="inode-search"
                type="search"
                value={query}
                onChange={(event) => setQuery(event.target.value)}
                placeholder="Search files"
                className="w-full rounded-sm border border-[#e3e3e0] bg-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003]/40 dark:border-[#3E3E3A] dark:bg-[#161615]"
            />

            {term.length > 0 && (
                <div className="absolute inset-x-0 top-full z-30 mt-1 max-h-80 overflow-y-auto rounded-sm border border-[#e3e3e0] bg-white p-2 shadow-lg dark:border-[#3E3E3A] dark:bg-[#161615]">
                    {term.length < MIN_SEARCH_LENGTH && (
                        <p className="px-1 py-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                            Type at least {MIN_SEARCH_LENGTH} characters to search.
                        </p>
                    )}

                    {searchError && (
                        <div role="alert" className="rounded-sm border border-red-300 bg-red-50 p-2 text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
                            Search failed: {searchError}
                        </div>
                    )}

                    {results && results.length === 0 && !searching && (
                        <p className="px-1 py-1 text-[#706f6c] dark:text-[#A1A09A]">No files match "{term}".</p>
                    )}

                    {results && results.length > 0 && (
                        <table className="w-full border-collapse text-left">
                            <thead>
                                <tr>
                                    <th className="px-2 py-1.5 text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                {results.map((item, index) => {
                                    // the root folder is named "/", so it is left out of the path
                                    const segments = item.parents.filter((parent) => parent.id !== ROOT_ID).map((parent) => parent.name);
                                    const prefix = segments.length > 0 ? `/${segments.join('/')}/` : '/';

                                    return (
                                        <tr key={`${item.parent_id}-${item.name}-${index}`} className="border-t border-[#e3e3e0] hover:bg-[#f5f5f3] dark:border-[#3E3E3A] dark:hover:bg-[#1f1f1d]">
                                            <td className="p-0">
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        setQuery('');
                                                        onSelect(item.parent_id);
                                                    }}
                                                    title="Open containing folder"
                                                    className="block w-full px-2 py-1.5 text-left"
                                                >
                                                    <span className="text-[#706f6c] dark:text-[#A1A09A]">{prefix}</span>
                                                    <span className="font-bold">{item.name}</span>
                                                </button>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    )}
                </div>
            )}
        </section>
    );
}
