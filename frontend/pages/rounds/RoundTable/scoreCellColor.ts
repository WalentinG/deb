import { yellow, green } from '@ant-design/colors';

export const scoreCellColor = (radiantScore: number, direScore: number) => {
  if (radiantScore > direScore) {
    return green[2];
  }

  if (radiantScore && direScore && radiantScore === direScore) {
    return yellow[1];
  }

  return 'white';
};
