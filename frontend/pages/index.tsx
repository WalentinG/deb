import type { NextPage } from 'next';
import Head from 'next/head';
import { Row, Col } from 'antd';
import { RoundTable } from '~components/RoundTable';
import { games } from '../../stubs';

const Home: NextPage = () => {
  return (
    <div>
      <Head>
        <title>DEB</title>
        <meta name="description" content="Deb" />
        <link rel="icon" href="/favicon.ico" />
      </Head>
      <main>
        <Row gutter={[24, 24]}>
          <Col md={12}>
            <RoundTable games={games} />
          </Col>
          <Col md={12}>
            <RoundTable games={games} />
          </Col>
          <Col md={12}>
            <RoundTable games={games} />
          </Col>
        </Row>
      </main>
    </div>
  );
};

export default Home;
