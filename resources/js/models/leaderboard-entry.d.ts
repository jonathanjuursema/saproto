type LeaderboardEntry = {
    id: number;
    leaderboard_id: number;
    user_id: number;
    points: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
}
