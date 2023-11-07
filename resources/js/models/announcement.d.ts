type Announcement = {
    id: number;
    description: string;
    content: string;
    display_from: string /* Date */;
    display_till: string /* Date */;
    show_guests: boolean;
    show_users: boolean;
    show_members: boolean;
    show_only_homepage: boolean;
    show_only_new: boolean;
    show_only_firstyear: boolean;
    show_only_active: boolean;
    show_as_popup: boolean;
    show_style: boolean;
    is_dismissable: boolean;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    bootstrap_style?: any;
    is_visible?: any;
    hash_map_id?: any;
    modal_id?: any;
    show_by_time?: any;
}
