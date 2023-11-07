type Event = {
    id: number;
    title: string;
    description: string;
    category_id: number | null;
    is_external: boolean;
    start: number;
    end: number;
    publication: number | null;
    location: string;
    maps_location: string | null;
    is_featured: boolean;
    involves_food: boolean;
    force_calendar_sync: boolean;
    photo_id: number | null;
    committee_id: number | null;
    summary: string | null;
    is_future?: any;
    formatted_date?: any;
}
