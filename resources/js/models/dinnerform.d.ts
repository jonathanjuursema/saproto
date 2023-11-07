type Dinnerform = {
    id: number;
    restaurant: string;
    description: string;
    url: string;
    start: string /* Date */;
    end: string /* Date */;
    event_id: number | null;
    helper_discount: number | null;
    regular_discount: number;
    closed: boolean;
    visible_home_page: boolean;
    ordered_by?: User | null;
    regular_discount_percentage?: any;
}
