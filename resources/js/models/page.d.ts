type Page = {
    id: number;
    title: string;
    slug: string;
    content: string;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    is_member_only: boolean;
    deleted_at: string /* Date */;
    featured_image_id: number | null;
    show_attachments: boolean;
}
