import { useQuery } from '@tanstack/react-query';
import ApiService from '@/services/api';
import { mapProductToCardInterface, CafeCardInterface } from '@/types/product.type';

type ProductsParams = {
  page?: number;
  perPage?: number;
  filters?: Record<string, any>;
};

export function useProducts(params: ProductsParams = {}) {
  const { page = 1, perPage = 15, filters = { is_active: true } } = params;

  return useQuery({
    queryKey: ['products', page, perPage, filters],
    queryFn: async (): Promise<CafeCardInterface[]> => {
      const response = await ApiService.getProducts(page, perPage, filters);
      return response.data.map(mapProductToCardInterface);
    },
    staleTime: 1000 * 60 * 5,
    gcTime: 1000 * 60 * 30,
  });
}