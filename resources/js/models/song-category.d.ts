type SongCategory = {
    id: number;
    name: string;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    songs?: CodexSong[] | null;
}
