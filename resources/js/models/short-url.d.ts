type ShortUrl = {
    id: number;
    description: string;
    url: string;
    target: string;
    clicks: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
};
