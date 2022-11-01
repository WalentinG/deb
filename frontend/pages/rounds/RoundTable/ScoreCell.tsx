import st from './ScoreCell.module.css';

export const ScoreCell = ({ score }: { score?: number }) => (
  <>
    { score ? <span className={st.bold}>{score}</span> : score}
  </>
);
