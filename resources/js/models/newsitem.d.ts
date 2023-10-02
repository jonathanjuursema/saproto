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
    url?: any;
};
