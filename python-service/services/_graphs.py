"""
    Map extracted structural components into a graph format
    Return a dictionary D3.json ready
"""

def build_graph(document_id, clear_title, tech_stack, relations):
    # 1. Safely extract raw JSON blocks 
    nodes = list(relations.get("nodes", []))
    links = list(relations.get("links", []))
    
    # 2. Build tracking references to check node ordering
    # Maps every node ID to its original index order from the LLM output array
    node_order = {node["id"]: idx for idx, node in enumerate(nodes)}
    
    validated_links = []
    seen_edges = set()
    
    # 3. INTERCEPT AND PREVENT CYCLICAL LOOPS
    for link in links:
        source = link.get("source")
        target = link.get("target")
        
        # Safe string formatting extraction wrapper
        src_id = source.get("id") if isinstance(source, dict) else source
        tgt_id = target.get("id") if isinstance(target, dict) else target
        
        # Drop broken paths or self-pointing nodes
        if not src_id or not tgt_id or src_id == tgt_id:
            continue
            
        # Deduplicate identical connections
        edge_key = f"{src_id}->{tgt_id}"
        if edge_key in seen_edges:
            continue
            
        # LINEAR ENFORCEMENT LAYER: 
        # Check if the target node appears earlier in the array than the source node.
        # If it does, the link is flowing backward. We reverse it to preserve the pipeline direction.
        if src_id in node_order and tgt_id in node_order:
            if node_order[tgt_id] < node_order[src_id]:
                # Swap directions to keep the graph flowing downwards
                src_id, tgt_id = tgt_id, src_id
                link["label"] = f"(Prerequisite) {link.get('label', '')}"

        # Reconstruct standard D3 link attributes safely
        validated_links.append({
            "source": src_id,
            "target": tgt_id,
            "label": link.get("label", "System dependency"),
            "value": 1
        })
        seen_edges.add(edge_key)

    # 4. Inject global system tracking nodes
    doc_node_id = f"doc_{document_id}"
    final_nodes = [{
        "id": doc_node_id,
        "group": "Actor",
        "description": f"Main file: {clear_title}"
    }] + nodes
    
    # Anchor loose technologies to the document node tracker safely
    existing_node_ids = {n["id"] for n in nodes}
    for tech in tech_stack:
        tech_id = tech.replace(" ", "")
        if tech_id not in existing_node_ids:
            final_nodes.append({
                "id": tech_id,
                "group": "Core System",
                "description": f"Technology: {tech}"
            })
            validated_links.append({
                "source": doc_node_id,
                "target": tech_id,
                "label": "Uses technology",
                "value": 1
            })

    return {"nodes": final_nodes, "links": validated_links}