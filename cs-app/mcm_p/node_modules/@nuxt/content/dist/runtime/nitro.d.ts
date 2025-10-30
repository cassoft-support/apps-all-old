import type { Collections, CollectionQueryBuilder, PageCollections, SurroundOptions, SQLOperator, QueryGroupFunction } from '@nuxt/content';
import type { H3Event } from 'h3';
interface ChainablePromise<T extends keyof PageCollections, R> extends Promise<R> {
    where(field: keyof PageCollections[T] | string, operator: SQLOperator, value?: unknown): ChainablePromise<T, R>;
    andWhere(groupFactory: QueryGroupFunction<PageCollections[T]>): ChainablePromise<T, R>;
    orWhere(groupFactory: QueryGroupFunction<PageCollections[T]>): ChainablePromise<T, R>;
    order(field: keyof PageCollections[T], direction: 'ASC' | 'DESC'): ChainablePromise<T, R>;
}
export declare const queryCollectionWithEvent: <T extends keyof Collections>(event: H3Event, collection: T) => CollectionQueryBuilder<Collections[T]>;
export declare function queryCollectionNavigationWithEvent<T extends keyof PageCollections>(event: H3Event, collection: T, fields?: Array<keyof PageCollections[T]>): ChainablePromise<T, ContentNavigationItem[]>;
export declare function queryCollectionItemSurroundingsWithEvent<T extends keyof PageCollections>(event: H3Event, collection: T, path: string, opts?: SurroundOptions<keyof PageCollections[T]>): ChainablePromise<T, ContentNavigationItem[]>;
export declare function queryCollectionSearchSectionsWithEvent(event: H3Event, collection: keyof Collections, opts?: {
    ignoredTags: string[];
}): ChainablePromise<string | number | symbol, {
    id: string;
    title: string;
    titles: string[];
    level: number;
    content: string;
}[]>;
export {};
