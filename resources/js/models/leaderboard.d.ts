type Leaderboard = {
    id: number;
    committee_id: number;
    name: string;
    featured: boolean;
    description: string;
    icon: string | null;
    points_name: string;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
};
