import MDCRenderer from '@nuxtjs/mdc/runtime/components/MDCRenderer.vue';
declare const debug: boolean | undefined;
declare const body: import("vue").ComputedRef<any>;
declare const isEmpty: import("vue").ComputedRef<boolean>;
declare const data: import("vue").ComputedRef<{
    [x: string]: any;
}>;
declare const componentsMap: import("vue").ComputedRef<Record<string, string | import("vue").DefineComponent<any, any, any>> | undefined>;
declare const __VLS_ctx: InstanceType<__VLS_PickNotAny<typeof __VLS_self, new () => {}>>;
declare var __VLS_5: {
    body: any;
    data: Record<string, any> & {
        [x: string]: any;
    };
    dataContentId: any;
};
type __VLS_Slots = __VLS_PrettifyGlobal<__VLS_OmitStringIndex<typeof __VLS_ctx.$slots> & {
    empty?: (props: typeof __VLS_5) => any;
}>;
declare const __VLS_self: import("vue").DefineComponent<import("vue").ExtractPropTypes<{
    /**
     * Content to render
     */
    value: {
        type: ObjectConstructor;
        required: true;
    };
    /**
     * Render only the excerpt
     */
    excerpt: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Root tag to use for rendering
     */
    tag: {
        type: StringConstructor;
        default: string;
    };
    /**
     * The map of custom components to use for rendering.
     */
    components: {
        type: ObjectConstructor;
        default: () => {};
    };
    data: {
        type: ObjectConstructor;
        default: () => {};
    };
    /**
     * Whether or not to render Prose components instead of HTML tags
     */
    prose: {
        type: BooleanConstructor;
        default: undefined;
    };
    /**
     * Root tag to use for rendering
     */
    class: {
        type: (StringConstructor | ObjectConstructor)[];
        default: undefined;
    };
    /**
     * Tags to unwrap separated by spaces
     * Example: 'ul li'
     */
    unwrap: {
        type: (StringConstructor | BooleanConstructor)[];
        default: boolean;
    };
}>, {
    MDCRenderer: typeof MDCRenderer;
    debug: typeof debug;
    body: typeof body;
    isEmpty: typeof isEmpty;
    data: typeof data;
    componentsMap: typeof componentsMap;
}, {}, {}, {}, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {}, string, import("vue").PublicProps, Readonly<import("vue").ExtractPropTypes<{
    /**
     * Content to render
     */
    value: {
        type: ObjectConstructor;
        required: true;
    };
    /**
     * Render only the excerpt
     */
    excerpt: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Root tag to use for rendering
     */
    tag: {
        type: StringConstructor;
        default: string;
    };
    /**
     * The map of custom components to use for rendering.
     */
    components: {
        type: ObjectConstructor;
        default: () => {};
    };
    data: {
        type: ObjectConstructor;
        default: () => {};
    };
    /**
     * Whether or not to render Prose components instead of HTML tags
     */
    prose: {
        type: BooleanConstructor;
        default: undefined;
    };
    /**
     * Root tag to use for rendering
     */
    class: {
        type: (StringConstructor | ObjectConstructor)[];
        default: undefined;
    };
    /**
     * Tags to unwrap separated by spaces
     * Example: 'ul li'
     */
    unwrap: {
        type: (StringConstructor | BooleanConstructor)[];
        default: boolean;
    };
}>> & Readonly<{}>, {
    data: Record<string, any>;
    class: string | Record<string, any>;
    tag: string;
    excerpt: boolean;
    components: Record<string, any>;
    prose: boolean;
    unwrap: string | boolean;
}, {}, {}, {}, string, import("vue").ComponentProvideOptions, true, {}, any>;
declare const __VLS_component: import("vue").DefineComponent<import("vue").ExtractPropTypes<{
    /**
     * Content to render
     */
    value: {
        type: ObjectConstructor;
        required: true;
    };
    /**
     * Render only the excerpt
     */
    excerpt: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Root tag to use for rendering
     */
    tag: {
        type: StringConstructor;
        default: string;
    };
    /**
     * The map of custom components to use for rendering.
     */
    components: {
        type: ObjectConstructor;
        default: () => {};
    };
    data: {
        type: ObjectConstructor;
        default: () => {};
    };
    /**
     * Whether or not to render Prose components instead of HTML tags
     */
    prose: {
        type: BooleanConstructor;
        default: undefined;
    };
    /**
     * Root tag to use for rendering
     */
    class: {
        type: (StringConstructor | ObjectConstructor)[];
        default: undefined;
    };
    /**
     * Tags to unwrap separated by spaces
     * Example: 'ul li'
     */
    unwrap: {
        type: (StringConstructor | BooleanConstructor)[];
        default: boolean;
    };
}>, {}, {}, {}, {}, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {}, string, import("vue").PublicProps, Readonly<import("vue").ExtractPropTypes<{
    /**
     * Content to render
     */
    value: {
        type: ObjectConstructor;
        required: true;
    };
    /**
     * Render only the excerpt
     */
    excerpt: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Root tag to use for rendering
     */
    tag: {
        type: StringConstructor;
        default: string;
    };
    /**
     * The map of custom components to use for rendering.
     */
    components: {
        type: ObjectConstructor;
        default: () => {};
    };
    data: {
        type: ObjectConstructor;
        default: () => {};
    };
    /**
     * Whether or not to render Prose components instead of HTML tags
     */
    prose: {
        type: BooleanConstructor;
        default: undefined;
    };
    /**
     * Root tag to use for rendering
     */
    class: {
        type: (StringConstructor | ObjectConstructor)[];
        default: undefined;
    };
    /**
     * Tags to unwrap separated by spaces
     * Example: 'ul li'
     */
    unwrap: {
        type: (StringConstructor | BooleanConstructor)[];
        default: boolean;
    };
}>> & Readonly<{}>, {
    data: Record<string, any>;
    class: string | Record<string, any>;
    tag: string;
    excerpt: boolean;
    components: Record<string, any>;
    prose: boolean;
    unwrap: string | boolean;
}, {}, {}, {}, string, import("vue").ComponentProvideOptions, true, {}, any>;
declare const _default: __VLS_WithSlots<typeof __VLS_component, __VLS_Slots>;
export default _default;
type __VLS_WithSlots<T, S> = T & {
    new (): {
        $slots: S;
    };
};
