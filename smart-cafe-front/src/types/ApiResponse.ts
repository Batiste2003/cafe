export interface ApiSuccessResponse<T = unknown> {
  success: true;
  message: string;
  data: T;
}

export interface ApiErrorResponse {
  success: false;
  message: string;
  error_code: string;
}

export interface PagingInfo {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface ApiSuccessWithPagingResponse<T = unknown> {
  success: true;
  message: string;
  data: T;
  paging: PagingInfo;
}

export type ApiResponse<T = unknown> = ApiSuccessResponse<T> | ApiErrorResponse;

export type ApiResponseWithPaging<T = unknown> = ApiSuccessWithPagingResponse<T> | ApiErrorResponse;
