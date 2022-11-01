import { FC } from 'react';
import { Table } from 'antd';
import { Game, Team } from '~api/debSchemas';
import { TeamCell } from './TeamCell';
import { ScoreCell } from './ScoreCell';
import { Title } from './Title';
import { scoreCellColor } from './scoreCellColor';

const { Column } = Table;

export interface Props {
  games: Game[];
}

export const RoundTable: FC<Props> = ({ games }) => {
  return (
    <Table<Game>
      showHeader={false}
      dataSource={games}
      pagination={false}
      bordered={true}
      title={() => <Title title="Round 1" />}
    >
      <Column
        title="Radiant Team"
        dataIndex="radiant_team"
        key="radiant_team"
        render={(team: Team) => <TeamCell team={team} />}
        onCell={(record: Game) => ({
          style: {
            backgroundColor: scoreCellColor(
              record.radiant_score ?? 0,
              record.dire_score ?? 0
            ),
          },
        })}
      />
      <Column
        title="Radiant Score"
        dataIndex="radiant_score"
        key="radiant_score"
        width={25}
        align="center"
        render={(score?: number) => <ScoreCell score={score} />}
        onCell={() => ({
          style: {
            borderRight: '2px solid #d9d9d9',
          },
        })}
      />
      <Column
        title="Dire Score"
        dataIndex="dire_score"
        key="dire_score"
        width={25}
        align="center"
        render={(score?: number) => <ScoreCell score={score} />}
      />
      <Column
        title="Dire Team"
        dataIndex="dire_team"
        key="dire_team"
        render={(team: Team) => <TeamCell team={team} />}
        onCell={(record: Game) => ({
          style: {
            backgroundColor: scoreCellColor(
              record.dire_score ?? 0,
              record.radiant_score ?? 0
            ),
          },
        })}
      />
    </Table>
  );
};
