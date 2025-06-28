import pandas as pd
from sklearn.linear_model import LinearRegression

# Загрузка CSV (убедись, что файл рядом с этим скриптом)
df = pd.read_csv("completed_workouts.csv")

# Предобработка
df['date_planned'] = pd.to_datetime(df['date_planned'])
df['date_completed'] = pd.to_datetime(df['date_completed'])
df['days_diff'] = (df['date_completed'] - df['date_planned']).dt.days

# Кодирование типа тренировки
df['workout_type_encoded'] = df['workout_type'].astype('category').cat.codes

# Подготовка данных
X = df[['days_diff', 'workout_type_encoded']]
y = df['duration']

# Обучение модели
model = LinearRegression()
model.fit(X, y)

# Прогноз
predicted = model.predict(X)
df['predicted_duration'] = predicted.round()

# Вывод результата
print(df[['workout_type', 'duration', 'predicted_duration']])