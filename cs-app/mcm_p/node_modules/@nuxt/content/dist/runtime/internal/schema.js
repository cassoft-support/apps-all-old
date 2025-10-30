export function getOrderedSchemaKeys(shape) {
  const keys = new Set([
    shape.id ? "id" : void 0,
    shape.title ? "title" : void 0,
    ...Object.keys(shape).sort()
  ].filter(Boolean));
  return Array.from(keys);
}
