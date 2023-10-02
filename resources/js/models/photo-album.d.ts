type PhotoAlbum = {
    id: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    name: string;
    date_create: number;
    date_taken: number;
    thumb_id: number;
    event_id: number | null;
    private: boolean;
    published: boolean;
};
