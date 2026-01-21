// Backend API response types
export interface Gallery {
  id: number;
  url: string;
  original_name: string;
  file_path?: string;
}

export interface ProductVariant {
  id: number;
  product_id: number;
  sku: string;
  price_cent_ht: number;
  price_euros: number;
  is_default: boolean;
  is_active?: boolean;
  stock_quantity?: number;
}

export interface ProductCategory {
  id: number;
  name: string;
  slug: string;
  description?: string;
  parent_id?: number;
}

export interface Product {
  id: number;
  name: string;
  slug: string;
  description?: string;
  is_active: boolean;
  is_featured: boolean;
  category?: ProductCategory;
  default_variant?: ProductVariant;
  variants?: ProductVariant[];
  gallery?: Gallery[];
  stores?: any[];
  created_at: string;
  updated_at?: string;
}

export interface ProductsApiResponse {
  success: boolean;
  message: string;
  data: Product[];
  paging?: {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
    from: number;
    to: number;
  };
}

// Component interface for CafeCard
export interface CafeCardInterface {
  id: string;
  name: string;
  description: string;
  price: string;
  imageUrl?: string;
  origin?: string;
  tags?: string[];
  badge?: string;
  slug: string;
  index?: number;
}

// Transformation function from backend Product to CafeCardInterface
export function mapProductToCardInterface(product: Product): CafeCardInterface {
  // Get price from default variant
  const price = product.default_variant?.price_euros || 0;
  const priceFormatted = `${price.toFixed(2)}€`;

  // Get first image from gallery
  const imageUrl = product.gallery && product.gallery.length > 0 
    ? product.gallery[0].url 
    : undefined;

  // Get category name for tags
  const tags: string[] = [];
  if (product.category) {
    tags.push(product.category.name);
  }

  // Add badge if featured
  const badge = product.is_featured ? 'Best-seller' : undefined;

  return {
    id: product.id.toString(),
    name: product.name,
    slug: product.slug,
    description: product.description || '',
    price: priceFormatted,
    imageUrl,
    origin: product.category?.name,
    tags,
    badge,
  };
}