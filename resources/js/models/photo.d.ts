type Photo = {
    id: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    file_id: number;
    album_id: number;
    date_taken: number;
    private: boolean;
    url?: any;
}
