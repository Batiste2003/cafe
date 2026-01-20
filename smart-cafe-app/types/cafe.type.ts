export interface CafeCardInterface {
  id?: string;
  name: string;
  slug: string;
  description: string;
  price: number;
  image: string;
  category: string[];
  index?: number;
}
