type WallstreetDrink = {
    id: number;
    end_time: number;
    start_time: number;
    price_increase: number;
    price_decrease: number;
    minimum_price: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    products?: Product[] | null;
};
