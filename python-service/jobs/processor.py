import json
import traceback
import phpserialize
from services.extractor import extract_knowledge
from notifier.webhook import notify

def audit_job(job):
    """Receives raw job to process:
        - extract text to summarize
        - extract job id to send returns back using webhook

    Args:
        job (string): job's content from client
    """
    try:
        data = json.loads(job)
        command = data["data"]["command"]

        # deserialized the php string from laravel
        deserialized = phpserialize.loads(
            command.encode(),
            decode_strings=True,
            object_hook=lambda name, attrs: attrs
        )

        text = deserialized.get("text")
        doc_id = deserialized.get("document_id")

        print(f"[processor] sending text {text}")
        
        # summarize text
        knowledge = extract_knowledge(doc_id, text)

        print(f"[processor] knowledge received {knowledge}")
        
        # extract tech stack
        tech_stack = knowledge["tech_stack"]
        
        # call notifier/webhook
        payload = {
            "summary": knowledge["onboarding_summary"],
            "execution_lifecycle": knowledge["execution_lifecycle"],
            "tech_stack": tech_stack,
        }

        print(f"notifying of payload {payload}")

        notify(doc_id, payload)
    except Exception as e:
        print(traceback.format_exc())
        print(f"[processor] Failed to process job: {e}")