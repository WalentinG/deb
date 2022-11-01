import { FC } from 'react';
import { Row, Col } from 'antd';
import st from './Title.module.css';

export interface Props {
  title: string;
}

export const Title: FC<Props> = ({ title }) => (
  <Row justify="center">
    <Col className={st.title}>{title}</Col>
  </Row>
);
