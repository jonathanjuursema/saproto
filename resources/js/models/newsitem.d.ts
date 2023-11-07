type Newsitem = {
    id: number;
    user_id: number;
    title: string;
    content: string;
    featured_image_id: number | null;
    published_at: string /* Date */ | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    deleted_at: string /* Date */;
    is_weekly: boolean;
    user?: User | null;
    events?: Event[] | null;
    featured_image?: StorageEntry | null;
    url?: string;
}
