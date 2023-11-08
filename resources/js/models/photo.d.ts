type Photo = {
    id: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    file_id: number;
    large_file_id: number | null;
    medium_file_id: number | null;
    small_file_id: number | null;
    tiny_file_id: number | null;
    album_id: number | null;
    date_taken: number;
    private: boolean;
    album?: PhotoAlbum | null;
    likes?: PhotoLikes[] | null;
    file?: StorageEntry | null;
    tiny_file?: StorageEntry | null;
    original_url?: string;
    large_url?: string;
    medium_url?: string;
    small_url?: string;
    tiny_url?: string;
}
