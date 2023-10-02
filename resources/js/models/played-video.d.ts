type PlayedVideo = {
    id: number;
    user_id: number | null;
    video_id: string;
    video_title: string;
    spotify_id: string | null;
    spotify_name: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
};
