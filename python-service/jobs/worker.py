import time
import redis
from config import REDIS_HOST, REDIS_PORT, REDIS_QUEUE_NAME
from .processor import audit_job

def start_worker():
    """Connect/wake up Redis to start processing jobs"""

    # keep set of connections open and reuse them
    pool = redis.ConnectionPool(
        host=REDIS_HOST,
        port=REDIS_PORT,
        decode_responses=True,
        health_check_interval=30,  # auto ping every 30 secs
    )

    client = redis.Redis(connection_pool=pool)

    while True:
        try:
            client.ping()
            print("[worker] Connected to Redis")
            break
        except redis.ConnectionError:
            print("[worker] Redis not ready, retrying in 2s...")
            time.sleep(2)

    print(f"[worker] Checking for jobs in queue {REDIS_QUEUE_NAME}")
    
    while True:
        try:
            job = client.brpop(
                REDIS_QUEUE_NAME, timeout=15
            )  # returns (key, value) or None / blocks until a job arrives

            if job:
                key, value = job
                print(f"[worker] Job found! {key}, {value}")

            # worker responsability is just to grab jobs from the queue and pass them along
                audit_job(value)
        except redis.exceptions.TimeoutError:
            continue  # queue is empty, keep polling
        except Exception as e:
            print(f"[worker] An unexpected error occurred: {e}. Stopping worker.")
            break
