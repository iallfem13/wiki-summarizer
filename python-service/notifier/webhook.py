import httpx
from config import LARAVEL_INTERNAL_SECRET, LARAVEL_WEBHOOK_URL

def notify(document_id, result):
    payload = {
        "document_id": document_id,
        **result # unpack payload with summary, tech_stack
    }
    
    headers = {
        "X-Internal-Secret": LARAVEL_INTERNAL_SECRET,
        "Accept": "application/json",
        "Content-Type": "application/json"
    }

    print ("Payload: ", payload)
    
    try:
        response = httpx.post(LARAVEL_WEBHOOK_URL, json=payload, headers=headers, timeout=5)
        response.raise_for_status()
        
        print(f"[Webhook] Notified Laravel for document {document_id} -> {response.status_code}")
    except httpx.HTTPStatusError as e: # as e gives the actual error details
        print(f"[Webhook] Laravel returned error: {e.response.status_code} - {e.response.text}")
    except httpx.RequestError as e:
        print(f"[Webhook] Unable to reach laravel service: {e}")