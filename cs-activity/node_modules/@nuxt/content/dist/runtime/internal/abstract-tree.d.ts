import type { MDCRoot } from '@nuxtjs/mdc';
import type { MinimalTree, MinimalNode } from '@nuxt/content';
export declare function compressTree(input: MDCRoot): MinimalTree;
export declare function decompressTree(input: MinimalTree): MDCRoot;
export declare function visit(tree: MinimalTree, checker: (node: MinimalNode) => boolean, visitor: (node: MinimalNode) => MinimalNode | undefined): void;
