import type { Component } from 'vue';
import type { ComponentProps, ComponentEmit } from 'vue-component-type-helpers';
type CloseEventArgType<T> = T extends (event: 'close', args_0: infer R) => void ? R : never;
export type OverlayOptions<OverlayAttrs = Record<string, any>> = {
    defaultOpen?: boolean;
    props?: OverlayAttrs;
    destroyOnClose?: boolean;
};
interface ManagedOverlayOptionsPrivate<T extends Component> {
    component?: T;
    id: symbol;
    isMounted: boolean;
    isOpen: boolean;
    resolvePromise?: (value: any) => void;
}
export type Overlay = OverlayOptions<Component> & ManagedOverlayOptionsPrivate<Component>;
interface OverlayInstance<T extends Component> extends Omit<ManagedOverlayOptionsPrivate<T>, 'component'> {
    id: symbol;
    result: Promise<CloseEventArgType<ComponentEmit<T>>>;
    open: (props?: ComponentProps<T>) => Omit<OverlayInstance<T>, 'open' | 'close' | 'patch' | 'modelValue' | 'resolvePromise'>;
    close: (value?: any) => void;
    patch: (props: Partial<ComponentProps<T>>) => void;
}
declare function _useOverlay(): {
    overlays: import("vue").ShallowReactive<Overlay[]>;
    open: <T extends Component>(id: symbol, props?: ComponentProps<T>) => {
        id: symbol;
        isMounted: boolean;
        isOpen: boolean;
        result: Promise<any>;
    };
    close: (id: symbol, value?: any) => void;
    create: <T extends Component>(component: T, _options?: OverlayOptions<ComponentProps<T>>) => OverlayInstance<T>;
    patch: <T extends Component>(id: symbol, props: Partial<ComponentProps<T>>) => void;
    unMount: (id: symbol) => void;
};
export declare const useOverlay: typeof _useOverlay;
export {};
