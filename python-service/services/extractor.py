"""
    Takes messy, overly complicated documentation and rewrites it for maximum clarity
"""

import json
from transformers import pipeline
from config import SUMMARIZATION_MODEL, SYSTEM_PROMPT

# _ => private
_pipeline = None

def get_pipeline():
    """Create singleton of HuggingFace model

    Returns:
        _type_: model pipeline
    """
    global _pipeline # tell python we are modifying the module-level variable, not creating a local one
    
    if _pipeline is None:
        # load model and store it in our global variable so it does not have to be done again on every call
        print("[summarizer] Loading model...")
        _pipeline = pipeline("text-generation", model=SUMMARIZATION_MODEL, max_new_tokens=1024, device_map="auto", clean_up_tokenization_spaces=False)
    
    return _pipeline

def extract_knowledge(document_id, original_text):
    user_content = f"Document ID: doc_{document_id}\n\nMessy Notes:\n{original_text}"
    
    messages = [
        {"role": "system", "content": SYSTEM_PROMPT},
        {"role": "user", "content": user_content}
    ]
    
    # Low temperature (0.1) forces the model to be strict, logical, and keep JSON valid
    result = get_pipeline()(
        messages, 
        max_new_tokens=600, 
        return_full_text=False,
        temperature=0.1 
    )

    raw_json = result[0]["generated_text"].strip()
    
    try:
        raw_json = cleanup_response(raw_json)
        print(raw_json)
        return json.loads(raw_json)
    except json.JSONDecodeError:
        print("[Error] JSON not clean. Raw text received:\n", raw_json)
        raise RuntimeError("Failed to parse optimized wiki data.")
    
def cleanup_response(raw_json):
    # Remove markdown block headers if present
    if raw_json.startswith("```json"):
        raw_json = raw_json[7:]
    #elif raw_json.startswith("```"):
    #    raw_json = raw_json[3:]
        
    # Remove markdown block footers if present
    if raw_json.endswith("```"):
        raw_json = raw_json[:-3]
        
    raw_json = raw_json.strip()

    return raw_json