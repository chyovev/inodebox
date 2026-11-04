import { useEffect, useState } from 'react';

export type InodeEntry = {
    id: number;
    name: string;
};

export type InodeContents = {
    name: string;
    directories: InodeEntry[];
    files: InodeEntry[];
};

type Props = {
    /** Inode to list on first render. Defaults to the root directory (1). */
    initialId?: number;
};

const ROOT_ID = 1;

export default function InodeTable({ initialId = ROOT_ID }: Props) {
    const [id, setId] = useState<number>(initialId);
    const [history, setHistory] = useState<number[]>([]);
    const [data, setData] = useState<InodeContents | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

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
    }, [id]);

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
                        </tr>
                    </thead>
                    <tbody>
                        {rows.length === 0 && !loading && data && (
                            <tr>
                                <td colSpan={2} className="px-3 py-4 text-center text-[#706f6c] dark:text-[#A1A09A]">
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
                                        row.name
                                    )}
                                </td>
                                <td className="px-3 py-2 text-[#706f6c] dark:text-[#A1A09A]">{row.type}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
