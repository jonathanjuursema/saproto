type StorageEntry = {
    id: number;
    filename: string;
    mime: string;
    original_filename: string;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    hash: string;
}
