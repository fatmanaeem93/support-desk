import joblib
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import classification_report, accuracy_score
from sklearn.model_selection import train_test_split
from sklearn.pipeline import Pipeline


def load_data(path: str = "tickets_dataset.csv") -> pd.DataFrame:
    return pd.read_csv(path)


def build_pipeline() -> Pipeline:

    return Pipeline([
        ("tfidf", TfidfVectorizer(
            max_features=2000,
            ngram_range=(1, 2),   # كلمة واحدة + زوج كلمات، بيحسن الدقة
            min_df=1,
        )),
        ("clf", LogisticRegression(max_iter=1000)),
    ])


def train_and_evaluate(df: pd.DataFrame, target_col: str, model_name: str) -> Pipeline:
    X_train, X_test, y_train, y_test = train_test_split(
        df["text"], df[target_col], test_size=0.2, random_state=42, stratify=df[target_col]
    )

    pipeline = build_pipeline()
    pipeline.fit(X_train, y_train)

    predictions = pipeline.predict(X_test)
    accuracy = accuracy_score(y_test, predictions)

    print(f"\n=== نتائج موديل {target_col} ===")
    print(f"الدقة (Accuracy): {accuracy:.2%}")
    print(classification_report(y_test, predictions, zero_division=0))

    joblib.dump(pipeline, model_name)
    print(f"تم حفظ الموديل في {model_name}")

    return pipeline


if __name__ == "__main__":
    df = load_data()

    print(f"إجمالي البيانات: {len(df)} صف")
    print(df["category"].value_counts())

    train_and_evaluate(df, "category", "category_model.joblib")
    train_and_evaluate(df, "priority", "priority_model.joblib")
