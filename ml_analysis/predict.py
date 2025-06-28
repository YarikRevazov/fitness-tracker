import sys
import pandas as pd
from sklearn.linear_model import LinearRegression

# Получаем параметры из командной строки
type_input = sys.argv[1]
days_input = int(sys.argv[2])

# Загружаем данные
df = pd.read_csv("completed_workouts.csv")

# Обработка дат
df['date_planned'] = pd.to_datetime(df['date_planned'])
df['date_completed'] = pd.to_datetime(df['date_completed'])
df['days_diff'] = (df['date_completed'] - df['date_planned']).dt.days

# Кодируем тип тренировки
df['workout_type_encoded'] = df['workout_type'].astype('category')
df['workout_type_code'] = df['workout_type_encoded'].cat.codes

# Создаём модель
X = df[['days_diff', 'workout_type_code']]
y = df['duration']
model = LinearRegression()
model.fit(X, y)

# Получаем код типа тренировки для входа
if type_input not in df['workout_type_encoded'].cat.categories:
    print("Тип тренировки не найден")
    sys.exit(1)

type_code = df['workout_type_encoded'].cat.categories.get_loc(type_input)

# Прогноз
X_new = [[days_input, type_code]]
prediction = model.predict(X_new)[0]
print(round(prediction))