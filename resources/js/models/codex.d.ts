type Codex = {
    id: number;
    name: string;
    export: string | null;
    description: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    songs?: CodexSong[] | null;
    texts?: CodexText[] | null;
    shuffles?: SongCategory[] | null;
}
