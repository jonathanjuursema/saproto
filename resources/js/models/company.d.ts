type Company = {
    id: number;
    name: string;
    url: string;
    excerpt: string;
    description: string;
    photo_id: number;
    on_carreer_page: boolean;
    in_logo_bar: boolean;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    on_membercard: boolean;
    membercard_excerpt: string | null;
    membercard_long: string | null;
    sort: number;
}
