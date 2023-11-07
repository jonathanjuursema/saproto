type CodexSong = {
    id: number;
    title: string;
    artist: string | null;
    lyrics: string;
    youtube: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    categories?: SongCategory[] | null;
    codices?: Codex[] | null;
}
