import pandas as pd
from collections import Counter

def count_gold_distribution(csv_path):
    df = pd.read_csv(csv_path, )
    print(df.head())
    cnt = Counter()
    for i, row in df.iterrows():
        cnt[row['Max_Label']] += 1
    print(cnt)



if __name__ == '__main__':
    count_gold_distribution('./Result_19.csv')