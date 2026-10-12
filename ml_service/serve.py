
from contextlib import asynccontextmanager

import joblib
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field

models: dict = {}


@asynccontextmanager
async def lifespan(app: FastAPI):
    # تحميل الموديلات مرة واحدة عند بدء تشغيل الخدمة، مش مع كل طلب
    try:
        models["category"] = joblib.load("category_model.joblib")
        models["priority"] = joblib.load("priority_model.joblib")
    except FileNotFoundError as e:
        raise RuntimeError(
            "الموديلات مش موجودة. شغّلي train.py الأول عشان تتدرب وتتحفظ."
        ) from e
    yield
    models.clear()


app = FastAPI(title="Support Ticket Classifier", lifespan=lifespan)


class TicketInput(BaseModel):
    text: str = Field(..., min_length=3, max_length=2000)


class ClassificationResult(BaseModel):
    category: str
    priority: str
    category_confidence: float
    priority_confidence: float


@app.post("/classify", response_model=ClassificationResult)
def classify_ticket(ticket: TicketInput) -> ClassificationResult:
    if "category" not in models:
        raise HTTPException(status_code=503, detail="الموديل لسه مش محمّل")

    category_model = models["category"]
    priority_model = models["priority"]

    category = category_model.predict([ticket.text])[0]
    category_proba = category_model.predict_proba([ticket.text]).max()

    priority = priority_model.predict([ticket.text])[0]
    priority_proba = priority_model.predict_proba([ticket.text]).max()

    return ClassificationResult(
        category=category,
        priority=priority,
        category_confidence=round(float(category_proba), 3),
        priority_confidence=round(float(priority_proba), 3),
    )


@app.get("/health")
def health_check() -> dict:
    return {"status": "ok", "models_loaded": len(models) == 2}
