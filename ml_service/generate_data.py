
import csv
import random

random.seed(42)

TEMPLATES = {
    "technical": [
        "التطبيق بيقفل فجأة لما بفتح صفحة {feature}",
        "فيه خطأ (error) بيظهر كل ما أحاول {action}",
        "الموقع بطيء جدًا في تحميل {feature}",
        "مش قادر أوصل لصفحة {feature} من الموبايل",
        "The app crashes every time I try to {action}",
        "Getting an error message when I open {feature}",
        "{feature} is not loading on my browser",
        "The system freezes during {action}",
    ],
    "billing": [
        "اتخصم مني مبلغ إضافي في فاتورة الشهر ده",
        "عايزة أفهم ليه سعر الاشتراك اتغير من غير إشعار",
        "لسه مستنية استرداد الفلوس بتاعتي من {days} يوم",
        "الفاتورة فيها رسوم مش فاهماها",
        "I was charged twice for the same subscription",
        "Why did my invoice amount change this month?",
        "Still waiting for my refund after {days} days",
        "There's an unexpected charge on my card",
    ],
    "account": [
        "مش قادرة أدخل على حسابي، نسيت الباسورد",
        "عايزة أغير الإيميل المرتبط بحسابي",
        "حسابي اتقفل من غير سبب واضح",
        "إزاي أحذف حسابي نهائيًا؟",
        "I can't log into my account anymore",
        "How do I change my registered email address?",
        "My account got suspended without explanation",
        "I want to permanently delete my account",
    ],
    "feature_request": [
        "ياريت تضيفوا خاصية {feature} في التطبيق",
        "التطبيق محتاج وضع الظلام (dark mode)",
        "ممكن تضيفوا دعم للغة العربية في {feature}؟",
        "اقتراح: يبقى في إشعارات لما {action}",
        "It would be great to have {feature} added",
        "Can you add dark mode support?",
        "Please consider adding export to {feature}",
        "Suggestion: notify users when {action}",
    ],
    "other": [
        "بس حبيت أشكركم على الخدمة الكويسة",
        "عندي سؤال عام عن سياسة الاستخدام",
        "إزاي أتواصل مع فريق المبيعات؟",
        "مين المسؤول عن الشراكات في الشركة؟",
        "Just wanted to say thanks for the great support",
        "I have a general question about your terms of service",
        "How can I get in touch with your sales team?",
        "Who handles partnership inquiries?",
    ],
}

FEATURES = ["الملف الشخصي", "لوحة التحكم", "صفحة الدفع", "التقارير", "reports", "the dashboard", "checkout page", "profile settings"]
ACTIONS = ["أحفظ بيانات", "أرفع صورة", "أعمل بحث", "save changes", "upload a file", "search"]
DAYS = ["5", "7", "10", "14"]

PRIORITY_RULES = {
    "billing": "high",
    "technical": "medium",
    "account": "high",
    "feature_request": "low",
    "other": "low",
}

def fill(template: str) -> str:
    return (
        template
        .replace("{feature}", random.choice(FEATURES))
        .replace("{action}", random.choice(ACTIONS))
        .replace("{days}", random.choice(DAYS))
    )


def generate(rows_per_category: int = 40) -> list[dict]:
    data = []
    for category, templates in TEMPLATES.items():
        for _ in range(rows_per_category):
            text = fill(random.choice(templates))
            priority = PRIORITY_RULES[category]
            if any(w in text.lower() for w in ["urgent", "asap", "مستعجل", "فورًا"]):
                priority = "urgent"
            data.append({"text": text, "category": category, "priority": priority})
    random.shuffle(data)
    return data


if __name__ == "__main__":
    rows = generate(rows_per_category=60)
    with open("tickets_dataset.csv", "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["text", "category", "priority"])
        writer.writeheader()
        writer.writerows(rows)
    print(f"تم توليد {len(rows)} صف في tickets_dataset.csv")
