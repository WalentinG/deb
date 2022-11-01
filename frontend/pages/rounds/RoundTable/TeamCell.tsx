import { Col, Row } from 'antd';
import Image from 'next/image';
import { Team } from '~api/debSchemas';

export const TeamCell = ({ team }: { team: Team }) => (
  <Row gutter={[12, 12]} align="middle" justify='center'>
    <Col>
      <Image src={team.radiant_avatar} alt={team.name} width={20} height={20} />
    </Col>
    <Col>{team.name}</Col>
    <Col>
      <Image src={team.dire_avatar} alt={team.name} width={20} height={20} />
    </Col>
  </Row>
);
