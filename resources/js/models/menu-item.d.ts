type MenuItem = {
    id: number;
    parent: number | null;
    menuname: string;
    url: string | null;
    page_id: number | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    order: number;
    is_member_only: boolean;
};
