type NarrowcastingItem = {
    id: number;
    name: string;
    image_id: number | null;
    campaign_start: number;
    campaign_end: number;
    slide_duration: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    youtube_id: string | null;
};
