export function compressTree(input) {
  return {
    type: "minimal",
    value: input.children.map(compressNode).filter((v) => v !== void 0)
  };
}
export function decompressTree(input) {
  return {
    type: "root",
    children: input.value.map(decompressNode)
  };
}
function decompressNode(input) {
  if (typeof input === "string") {
    return {
      type: "text",
      value: input
    };
  }
  const [tag, props, ...children] = input;
  return {
    type: "element",
    tag,
    props,
    children: children.map(decompressNode)
  };
}
function compressNode(input) {
  if (input.type === "comment") {
    return void 0;
  }
  if (input.type === "text") {
    return input.value;
  }
  if (input.tag === "code" && input.props?.className && input.props.className.length === 0) {
    delete input.props.className;
  }
  return [
    input.tag,
    input.props || {},
    ...input.children.map(compressNode).filter((v) => v !== void 0)
  ];
}
export function visit(tree, checker, visitor) {
  function walk(node, parent, index) {
    if (checker(node)) {
      const res = visitor(node);
      if (res !== void 0) {
        parent[index] = res;
      }
    }
    if (Array.isArray(node) && node.length > 2) {
      for (let i = 2; i < node.length; i++) {
        walk(node[i], node, i);
      }
    }
  }
  tree.value.forEach((node, i) => {
    walk(node, tree.value, i);
  });
}
