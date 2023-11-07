type FeedbackVote = {
    id: number;
    user_id: number;
    feedback_id: number;
    vote: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    feedback?: Feedback | null;
    user?: User | null;
}
