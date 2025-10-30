import * as _nuxt_schema from '@nuxt/schema';
import { BuiltinTheme, ThemeRegistrationRaw, BuiltinLanguage, LanguageRegistration, ThemeRegistrationAny } from 'shiki';
import { ListenOptions } from 'listhen';
import { Toc, Highlighter } from '@nuxtjs/mdc';
export { Toc, TocLink } from '@nuxtjs/mdc';
import * as z$1 from 'zod';
import { ZodRawShape, ZodObject, z as z$2 } from 'zod';
import { zodToJsonSchema } from 'zod-to-json-schema';
import * as c12 from 'c12';
import { Primitive, Connector } from 'db0';

interface GitInfo {
    name: string;
    owner: string;
    url: string;
}

type MinimalText = string;
type MinimalElement = [string, Record<string, unknown>, ...MinimalNode[]];
type MinimalNode = MinimalElement | MinimalText;
type MinimalTree = {
    type: 'minimal';
    value: MinimalNode[];
};

interface ContentFile extends Record<string, unknown> {
    id: string;
    body: string;
    path: string;
    dirname?: string;
    extension?: string;
}
interface TransformedContent {
    id: string;
    [key: string]: unknown;
}
interface TransformContentOptions {
    transformers?: ContentTransformer[];
    [key: string]: unknown;
}
type ContentTransformer = {
    name: string;
    extensions: string[];
    parse(file: ContentFile, options: Record<string, unknown>): Promise<TransformedContent> | TransformedContent;
    transform?(content: TransformedContent, options: Record<string, unknown>): Promise<TransformedContent> | TransformedContent;
} | {
    name: string;
    extensions: string[];
    parse?(file: ContentFile, options: Record<string, unknown>): Promise<TransformedContent> | TransformedContent;
    transform(content: TransformedContent, options: Record<string, unknown>): Promise<TransformedContent> | TransformedContent;
};
interface MarkdownPlugin extends Record<string, unknown> {
    instance?: unknown;
    options?: Record<string, unknown>;
}
interface MarkdownOptions {
    /**
     * Enable/Disable MDC components.
     */
    mdc: boolean;
    toc: {
        /**
         * Maximum heading depth to include in the table of contents.
         */
        depth: number;
        searchDepth: number;
    };
    tags: Record<string, string>;
    remarkPlugins: Record<string, false | MarkdownPlugin>;
    rehypePlugins: Record<string, false | MarkdownPlugin>;
    highlight?: {
        highlighter?: Highlighter;
        [key: string]: unknown;
    };
}
declare const ContentFileExtension: {
    readonly Markdown: "md";
    readonly Yaml: "yaml";
    readonly Yml: "yml";
    readonly Json: "json";
    readonly Csv: "csv";
    readonly Xml: "xml";
};
declare const ContentFileType: {
    readonly Markdown: "markdown";
    readonly Yaml: "yaml";
    readonly Json: "json";
    readonly Csv: "csv";
};
interface MarkdownRoot extends MinimalTree {
    props?: Record<string, unknown>;
    toc?: Toc;
}
type ParsedContentFile = Record<string, unknown>;

interface SlugifyOptions {
    /**
     * Characters to remove from the slug
     *
     * @default undefined
     */
    remove?: RegExp;
    replacement?: string;
    /**
     * Convert the slug to lowercase
     *
     * @default true
     */
    lower?: boolean;
    strict?: boolean;
    locale?: string;
    trim?: boolean;
}
interface PathMetaOptions {
    /**
     * If set to `true`, the path will be prefixed with a leading slash.
     *
     * @default true
     */
    forceLeadingSlash?: boolean;
    /**
     * Slugify options
     *
     * @see https://github.com/simov/slugify#options
     */
    slugifyOptions?: SlugifyOptions;
}

interface D1DatabaseConfig {
    type: 'd1';
    bindingName: string;
    /**
     * @deprecated Use `bindingName` instead
     */
    binding?: string;
}
interface SqliteDatabaseConfig {
    type: 'sqlite';
    filename: string;
}
type PostgreSQLDatabaseConfig = {
    type: 'postgres';
    url: string;
};
type LibSQLDatabaseConfig = {
    type: 'libsql';
    /**
     * The URL of the libSQL/Turso database
     */
    url: string;
    /**
     * The authentication token for the libSQL/Turso database
     */
    authToken: string;
};
interface PreviewOptions {
    /**
     * Enable preview in production by setting API URL
     */
    api?: string;
    /**
     * Enable preview mode in development
     */
    dev?: boolean;
    /**
     * Override Git information for preview validation
     */
    gitInfo?: GitInfo;
}
interface ModuleOptions {
    /**
     * @private
     * @default { type: 'sqlite', filename: '.data/content/local.db' }
     */
    _localDatabase: SqliteDatabaseConfig | D1DatabaseConfig;
    /**
     * Production database configuration
     * @default { type: 'sqlite', filename: './contents.sqlite' }
     */
    database: D1DatabaseConfig | SqliteDatabaseConfig | PostgreSQLDatabaseConfig | LibSQLDatabaseConfig;
    /**
     * Preview mode configuration
     * @default {}
     */
    preview?: PreviewOptions;
    /**
     * Development HMR
     * @default { enabled: true }
     */
    watch?: Partial<ListenOptions> & {
        enabled?: boolean;
    };
    renderer: {
        /**
         * Tags will be used to replace markdown components and render custom components instead of default ones.
         *
         * @default {}
         */
        alias?: Record<string, string>;
        /**
         * Anchor link generation config
         *
         * @default {}
         */
        anchorLinks?: boolean | Partial<Record<'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6', boolean>>;
    };
    build: {
        /**
         * List of user-defined transformers.
         */
        transformers?: string[];
        markdown: {
            /**
             * Control behavior of Table of Contents generation
             */
            toc?: {
                /**
                 * Maximum heading depth that includes in the table of contents.
                 *
                 * @default 2
                 */
                depth?: number;
                /**
                 * Maximum depth of nested tags to search for heading.
                 *
                 * @default 2
                 */
                searchDepth?: number;
            };
            /**
             * Register custom remark plugin to provide new feature into your markdown contents.
             * Checkout: https://github.com/remarkjs/remark/blob/main/doc/plugins.md
             *
             * @default []
             */
            remarkPlugins?: Record<string, false | MarkdownPlugin>;
            /**
             * Register custom remark plugin to provide new feature into your markdown contents.
             * Checkout: https://github.com/rehypejs/rehype/blob/main/doc/plugins.md
             *
             * @default []
             */
            rehypePlugins?: Record<string, false | MarkdownPlugin>;
            /**
             * Content module uses `shiki` to highlight code blocks.
             * You can configure Shiki options to control its behavior.
             */
            highlight?: false | {
                /**
                 * Default theme that will be used for highlighting code blocks.
                 */
                theme?: BuiltinTheme | {
                    default: BuiltinTheme | ThemeRegistrationRaw | string;
                    [theme: string]: BuiltinTheme | ThemeRegistrationRaw | string;
                };
                /**
                 * Preloaded languages that will be available for highlighting code blocks.
                 *
                 * @deprecated Use `langs` instead
                 */
                preload?: (BuiltinLanguage | LanguageRegistration)[];
                /**
                 * Languages to be bundled loaded by Shiki
                 *
                 * All languages used has to be included in this list at build time, to create granular bundles.
                 *
                 * Unlike the `preload` option, when this option is provided, it will override the default languages.
                 *
                 * @default ['js','jsx','json','ts','tsx','vue','css','html','vue','bash','md','mdc','yaml']
                 */
                langs?: (BuiltinLanguage | LanguageRegistration)[];
                /**
                 * Additional themes to be bundled loaded by Shiki.
                 */
                themes?: (BuiltinTheme | ThemeRegistrationAny)[];
            };
        };
        pathMeta?: PathMetaOptions;
        /**
         * Options for yaml parser.
         *
         * @default {}
         */
        yaml?: false | Record<string, unknown>;
        /**
         * Options for csv parser.
         *
         * @default {}
         */
        csv?: false | {
            json?: boolean;
            delimiter?: string;
        };
    };
    experimental?: {
        /**
         * Use Node.js native SQLite bindings instead of `better-sqlite3` if available
         * Node.js SQLite introduced in v22.5.0
         *
         * @default false
         * @deprecated Use `sqliteConnector: 'native'` instead
         */
        nativeSqlite?: boolean;
        /**
         * Use given SQLite connector instead of `better-sqlite3` if available
         *
         * @default undefined
         */
        sqliteConnector?: SQLiteConnector;
    };
}
interface RuntimeConfig {
    content: {
        version: string;
        databaseVersion: string;
        database: D1DatabaseConfig | SqliteDatabaseConfig | PostgreSQLDatabaseConfig;
        localDatabase: SqliteDatabaseConfig;
        integrityCheck: boolean;
    };
}
interface PublicRuntimeConfig {
    preview: {
        api?: string;
        iframeMessagingAllowedOrigins?: string;
    };
}
type SQLiteConnector = 'native' | 'sqlite3' | 'better-sqlite3';

declare const metaSchema: z$1.ZodObject<{
    id: z$1.ZodString;
    stem: z$1.ZodString;
    extension: z$1.ZodEnum<["json" | "md" | "yaml" | "yml" | "csv" | "xml"]>;
    meta: z$1.ZodRecord<z$1.ZodString, z$1.ZodAny>;
}, "strip", z$1.ZodTypeAny, {
    meta: Record<string, any>;
    id: string;
    extension: "json" | "md" | "yaml" | "yml" | "csv" | "xml";
    stem: string;
}, {
    meta: Record<string, any>;
    id: string;
    extension: "json" | "md" | "yaml" | "yml" | "csv" | "xml";
    stem: string;
}>;
declare const pageSchema: z$1.ZodObject<{
    path: z$1.ZodString;
    title: z$1.ZodString;
    description: z$1.ZodString;
    seo: z$1.ZodDefault<z$1.ZodOptional<z$1.ZodIntersection<z$1.ZodObject<{
        title: z$1.ZodOptional<z$1.ZodString>;
        description: z$1.ZodOptional<z$1.ZodString>;
    }, "strip", z$1.ZodTypeAny, {
        title?: string | undefined;
        description?: string | undefined;
    }, {
        title?: string | undefined;
        description?: string | undefined;
    }>, z$1.ZodRecord<z$1.ZodString, z$1.ZodAny>>>>;
    body: z$1.ZodObject<{
        type: z$1.ZodString;
        children: z$1.ZodAny;
        toc: z$1.ZodAny;
    }, "strip", z$1.ZodTypeAny, {
        type: string;
        children?: any;
        toc?: any;
    }, {
        type: string;
        children?: any;
        toc?: any;
    }>;
    navigation: z$1.ZodDefault<z$1.ZodOptional<z$1.ZodUnion<[z$1.ZodBoolean, z$1.ZodObject<{
        title: z$1.ZodString;
        description: z$1.ZodString;
        icon: z$1.ZodString;
    }, "strip", z$1.ZodTypeAny, {
        title: string;
        description: string;
        icon: string;
    }, {
        title: string;
        description: string;
        icon: string;
    }>]>>>;
}, "strip", z$1.ZodTypeAny, {
    title: string;
    path: string;
    body: {
        type: string;
        children?: any;
        toc?: any;
    };
    description: string;
    seo: {
        title?: string | undefined;
        description?: string | undefined;
    } & Record<string, any>;
    navigation: boolean | {
        title: string;
        description: string;
        icon: string;
    };
}, {
    title: string;
    path: string;
    body: {
        type: string;
        children?: any;
        toc?: any;
    };
    description: string;
    seo?: ({
        title?: string | undefined;
        description?: string | undefined;
    } & Record<string, any>) | undefined;
    navigation?: boolean | {
        title: string;
        description: string;
        icon: string;
    } | undefined;
}>;

interface PageCollections {
}
interface Collections {
}
type CollectionType = 'page' | 'data';
type CollectionSource = {
    include: string;
    prefix?: string;
    exclude?: string[];
    repository?: string;
    authToken?: string;
    authBasic?: {
        username: string;
        password: string;
    };
    cwd?: string;
};
interface ResolvedCollectionSource extends CollectionSource {
    _resolved: true;
    prepare?: (opts: {
        rootDir: string;
    }) => Promise<void>;
    getKeys?: () => Promise<string[]>;
    getItem?: (path: string) => Promise<string>;
    cwd: string;
}
interface CustomCollectionSource {
    prepare?: (opts: {
        rootDir: string;
    }) => Promise<void>;
    getKeys: () => Promise<string[]>;
    getItem: (path: string) => Promise<string>;
}
interface ResolvedCustomCollectionSource extends ResolvedCollectionSource {
    _custom: true;
}
interface PageCollection<T extends ZodRawShape = ZodRawShape> {
    type: 'page';
    source?: string | CollectionSource | CollectionSource[] | ResolvedCustomCollectionSource;
    schema?: ZodObject<T>;
}
interface DataCollection<T extends ZodRawShape = ZodRawShape> {
    type: 'data';
    source?: string | CollectionSource | CollectionSource[] | ResolvedCustomCollectionSource;
    schema: ZodObject<T>;
}
type Collection<T extends ZodRawShape = ZodRawShape> = PageCollection<T> | DataCollection<T>;
interface DefinedCollection<T extends ZodRawShape = ZodRawShape> {
    type: CollectionType;
    source: ResolvedCollectionSource[] | undefined;
    schema: ZodObject<T>;
    extendedSchema: ZodObject<T>;
    fields: Record<string, 'string' | 'number' | 'boolean' | 'date' | 'json'>;
}
interface ResolvedCollection<T extends ZodRawShape = ZodRawShape> {
    name: string;
    tableName: string;
    type: CollectionType;
    source: ResolvedCollectionSource[] | undefined;
    schema: ZodObject<T>;
    extendedSchema: ZodObject<T>;
    fields: Record<string, 'string' | 'number' | 'boolean' | 'date' | 'json'>;
    /**
     * Whether the collection is private or not.
     * Private collections will not be available in the runtime.
     */
    private: boolean;
}
interface CollectionInfo {
    name: string;
    pascalName: string;
    tableName: string;
    source: ResolvedCollectionSource[];
    type: CollectionType;
    schema: ReturnType<typeof zodToJsonSchema>;
    fields: Record<string, 'string' | 'number' | 'boolean' | 'date' | 'json'>;
    tableDefinition: string;
}
interface CollectionItemBase {
    id: string;
    stem: string;
    extension: string;
    meta: Record<string, unknown>;
}
interface PageCollectionItemBase extends CollectionItemBase {
    path: string;
    title: string;
    description: string;
    seo: {
        title?: string;
        description?: string;
        [key: string]: unknown;
    };
    body: MarkdownRoot;
    navigation?: boolean | {
        title: string;
        description: string;
        icon: string;
    };
}
interface DataCollectionItemBase extends CollectionItemBase {
}

interface ParserOptions {
    pathMeta: PathMetaOptions;
    markdown: {
        compress: boolean;
        rehypePlugins: Record<string, unknown>;
        remarkPlugins: Record<string, unknown>;
        [key: string]: unknown;
    };
}
interface FileBeforeParseHook {
    file: ContentFile;
    collection: ResolvedCollection;
    parserOptions: ParserOptions;
}
interface FileAfterParseHook {
    file: ContentFile;
    content: ParsedContentFile;
    collection: ResolvedCollection;
}
declare module '@nuxt/schema' {
    interface NuxtHooks {
        'content:file:beforeParse': (ctx: FileBeforeParseHook) => Promise<void> | void;
        'content:file:afterParse': (ctx: FileAfterParseHook) => Promise<void> | void;
    }
}

interface ContentNavigationItem {
    title: string;
    path: string;
    stem?: string;
    children?: ContentNavigationItem[];
    page?: false;
    [key: string]: unknown;
}

interface SurroundOptions<T> {
    before?: number;
    after?: number;
    fields?: Array<T>;
}

type SQLOperator = '=' | '>' | '<' | '<>' | 'IN' | 'BETWEEN' | 'NOT BETWEEN' | 'IS NULL' | 'IS NOT NULL' | 'LIKE' | 'NOT LIKE';
type QueryGroupFunction<T> = (group: CollectionQueryGroup<T>) => CollectionQueryGroup<T>;
interface CollectionQueryBuilder<T> {
    path(path: string): CollectionQueryBuilder<T>;
    select<K extends keyof T>(...fields: K[]): CollectionQueryBuilder<Pick<T, K>>;
    order(field: keyof T, direction: 'ASC' | 'DESC'): CollectionQueryBuilder<T>;
    skip(skip: number): CollectionQueryBuilder<T>;
    limit(limit: number): CollectionQueryBuilder<T>;
    all(): Promise<T[]>;
    first(): Promise<T | null>;
    count(field?: keyof T | '*', distinct?: boolean): Promise<number>;
    where(field: string, operator: SQLOperator, value?: unknown): CollectionQueryBuilder<T>;
    andWhere(groupFactory: QueryGroupFunction<T>): CollectionQueryBuilder<T>;
    orWhere(groupFactory: QueryGroupFunction<T>): CollectionQueryBuilder<T>;
}
interface CollectionQueryGroup<T> {
    where(field: string, operator: SQLOperator, value?: unknown): CollectionQueryGroup<T>;
    andWhere(groupFactory: QueryGroupFunction<T>): CollectionQueryGroup<T>;
    orWhere(groupFactory: QueryGroupFunction<T>): CollectionQueryGroup<T>;
}

type CacheEntry = {
    id: string;
    value: string;
    checksum: string;
};
type DatabaseBindParams = Primitive[];
type DatabaseBindable = number | string | boolean | null | undefined;
interface DatabaseAdapter {
    first<T>(sql: string, params?: DatabaseBindParams): Promise<T | null | undefined>;
    all<T>(sql: string, params?: DatabaseBindParams): Promise<T[]>;
    exec(sql: string, params?: DatabaseBindParams): Promise<unknown>;
}
type DatabaseAdapterFactory<Options> = (otps?: Options) => DatabaseAdapter;
interface LocalDevelopmentDatabase {
    fetchDevelopmentCache(): Promise<Record<string, CacheEntry>>;
    fetchDevelopmentCacheForKey(key: string): Promise<CacheEntry | undefined>;
    insertDevelopmentCache(id: string, checksum: string, parsedContent: string): void;
    deleteDevelopmentCache(id: string): void;
    dropContentTables(): void;
    exec(sql: string): void;
    close(): void;
    database?: Connector;
}

interface ParsedContentv2 {
    _id: string;
    _source?: string;
    _path?: string;
    title?: string;
    _draft?: boolean;
    _partial?: boolean;
    _locale?: string;
    _type?: 'markdown' | 'yaml' | 'json' | 'csv';
    _file?: string;
    _extension?: 'md' | 'yaml' | 'yml' | 'json' | 'json5' | 'csv';
    excerpt?: Record<string, unknown>;
    body: Record<string, unknown> | null;
    layout?: string;
    [key: string]: unknown;
}
interface DraftSyncData {
    files: DraftSyncFile[];
    additions: DraftFile[];
    deletions: DraftFile[];
}
interface DraftSyncFile {
    path: string;
    parsed?: ParsedContentv2;
}
interface DraftFile {
    path: string;
    parsed?: ParsedContentv2;
    new?: boolean;
    oldPath?: string;
    pathMeta?: Record<string, unknown>;
}
type FileStatus = 'created' | 'updated' | 'deleted' | 'renamed';
interface PreviewFile {
    id: number;
    name: string;
    nameWithoutPrefix: string;
    type: 'file';
    path: string;
    pathPreview: string;
    pathWithoutRoot: string;
    pathRoute: string;
    status: FileStatus;
    content: string;
    updatedAt: string;
    parsed: ParsedContentv2;
}
interface FileChangeMessagePayload {
    additions: PreviewFile[];
    deletions: {
        path: string;
    }[];
}
interface FileSelectMessagePayload {
    path: string;
}
type FileMessageType = 'nuxt-content:editor:file-selected' | 'nuxt-content:editor:file-changed' | 'nuxt-content:editor:media-changed' | 'nuxt-content:config:file-changed';
interface FileMessageData {
    type: FileMessageType;
    payload: FileChangeMessagePayload | FileSelectMessagePayload;
    navigate: boolean;
}

declare function defineCollection<T extends ZodRawShape>(collection: Collection<T>): DefinedCollection;
declare function defineCollectionSource(source: CustomCollectionSource): ResolvedCustomCollectionSource;

type NuxtContentConfig = {
    collections: Record<string, DefinedCollection>;
};
declare const defineContentConfig: c12.DefineConfig<NuxtContentConfig, c12.ConfigLayerMeta>;

declare const defineTransformer: (transformer: ContentTransformer) => ContentTransformer;

declare module 'zod' {
    interface ZodTypeDef {
        editor?: EditorOptions;
    }
    interface ZodType {
        editor(options: EditorOptions): this;
    }
}
interface EditorOptions {
    input?: 'media' | 'icon';
    hidden?: boolean;
}
declare const z: typeof z$2;

declare const _default: _nuxt_schema.NuxtModule<ModuleOptions, ModuleOptions, false>;

export { type CacheEntry, type Collection, type CollectionInfo, type CollectionItemBase, type CollectionQueryBuilder, type CollectionQueryGroup, type CollectionSource, type CollectionType, type Collections, type ContentFile, ContentFileExtension, ContentFileType, type ContentNavigationItem, type ContentTransformer, type CustomCollectionSource, type D1DatabaseConfig, type DataCollection, type DataCollectionItemBase, type DatabaseAdapter, type DatabaseAdapterFactory, type DatabaseBindParams, type DatabaseBindable, type DefinedCollection, type DraftFile, type DraftSyncData, type DraftSyncFile, type FileAfterParseHook, type FileBeforeParseHook, type FileChangeMessagePayload, type FileMessageData, type FileMessageType, type FileSelectMessagePayload, type FileStatus, type LibSQLDatabaseConfig, type LocalDevelopmentDatabase, type MarkdownOptions, type MarkdownPlugin, type MarkdownRoot, type MinimalElement, type MinimalNode, type MinimalText, type MinimalTree, type ModuleOptions, type PageCollection, type PageCollectionItemBase, type PageCollections, type ParsedContentFile, type ParsedContentv2, type PostgreSQLDatabaseConfig, type PreviewFile, type PreviewOptions, type PublicRuntimeConfig, type QueryGroupFunction, type ResolvedCollection, type ResolvedCollectionSource, type ResolvedCustomCollectionSource, type RuntimeConfig, type SQLOperator, type SQLiteConnector, type SqliteDatabaseConfig, type SurroundOptions, type TransformContentOptions, type TransformedContent, _default as default, defineCollection, defineCollectionSource, defineContentConfig, defineTransformer, metaSchema, pageSchema, z };
