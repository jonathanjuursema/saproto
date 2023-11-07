type Feedback = {
    id: number;
    user_id: number;
    feedback_category_id: number;
    feedback: string;
    reviewed: boolean;
    accepted: boolean | null;
    reply: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    deleted_at: string /* Date */;
    user?: User | null;
    category?: FeedbackCategory | null;
    votes?: FeedbackVote[] | null;
}
