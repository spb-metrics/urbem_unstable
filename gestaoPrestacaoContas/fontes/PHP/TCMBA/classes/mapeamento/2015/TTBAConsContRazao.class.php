<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 19/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 19/10/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAConsContRazao extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function TTBAConsContRazao()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , ".$this->getDado('exercicio')."::VARCHAR||".$this->getDado('mes')." AS competencia
                        , '' AS reservado_tcm
                        , tabela.*

                    FROM (
                            SELECT cod_estrutural AS conta_contabil
                                 , SUM(vl_deb_ant) AS vl_deb_ant
                                 , SUM(vl_cred_ant) AS vl_cred_ant
                                 , SUM(vl_deb_mes_ant) AS vl_deb_mes_ant
                                 , SUM(vl_cred_mes_ant) AS vl_cred_mes_ant
                                 , SUM(vl_deb_mes) AS vl_deb_mes
                                 , SUM(vl_cred_mes) AS vl_cred_mes
                                 , SUM(vl_deb_ate_mes) AS vl_deb_ate_mes
                                 , SUM(vl_cred_ate_mes) AS vl_cred_ate_mes
                                 , SUM(vl_deb_exercicio) AS vl_deb_exercicio
                                 , SUM(vl_cred_exercicio) AS vl_cred_exercicio

                            FROM (
                                    -- DEBITO

                                    SELECT cod_estrutural
                                         , 0.00 AS vl_deb_ant
                                         , 0.00 AS vl_cred_ant
                                         , COALESCE(SUM(saldo_anterior),0.00) AS vl_deb_mes_ant
                                         , 0.00 AS vl_cred_mes_ant
                                         , COALESCE(SUM(vl_lancamento),0.00) AS vl_deb_mes
                                         , 0.00 AS vl_cred_mes
                                         , (COALESCE(SUM(saldo_anterior),0.00) + COALESCE(SUM(vl_lancamento),0.00)) AS vl_deb_ate_mes
                                         , 0.00 AS vl_cred_ate_mes
                                         , 0.00 AS vl_deb_exercicio
                                         , 0.00 AS vl_cred_exercicio
                                    FROM contabilidade.fn_relatorio_razao ( '".$this->getDado('exercicio')."'
                                                                           , ''
                                                                           , '0.0.0.0.0.00.00.00.00.00'
                                                                           , '9.9.9.9.9.99.99.99.99.99'
                                                                           , '".$this->getDado('dt_inicial')."'
                                                                           , '".$this->getDado('dt_final')."'
                                                                           , '".$this->getDado('entidades')."'
                                                                           , '".$this->getDado('dt_inicial_ant')."'
                                                                           , '".$this->getDado('dt_final_ant')."'
                                                                           , 'N'
                                                                           , 'N'
                                                        ) AS retorno ( 
                                                                       cod_lote          INTEGER                                
                                                                      ,sequencia         INTEGER                                
                                                                      ,cod_historico     INTEGER                                
                                                                      ,nom_historico     VARCHAR                                
                                                                      ,complemento       VARCHAR                                
                                                                      ,observacao        TEXT                                   
                                                                      ,exercicio         CHAR(4)                                
                                                                      ,cod_entidade      INTEGER                                
                                                                      ,tipo              CHAR(1)                                
                                                                      ,vl_lancamento     NUMERIC                                
                                                                      ,tipo_valor        CHAR(1)                                
                                                                      ,dt_lote           VARCHAR                                
                                                                      ,dt_lote_formatado DATE                                
                                                                      ,cod_plano         INTEGER                                
                                                                      ,cod_estrutural    VARCHAR                                
                                                                      ,nom_conta         VARCHAR                                
                                                                      ,contra_partida    NUMERIC                                
                                                                      ,saldo_anterior    NUMERIC                                
                                                                      ,num_lancamentos   INTEGER      
                                                                    )
                                    WHERE tipo <> 'I'
                                      AND saldo_anterior > 0
                                      AND tipo_valor = 'D'
                                    GROUP BY cod_estrutural

                                    UNION ALL

                                    -- CREDITO

                                    SELECT cod_estrutural
                                         , 0.00 AS vl_deb_ant
                                         , 0.00 AS vl_cred_ant
                                         , 0.00 AS vl_deb_mes_ant
                                         , COALESCE(SUM(saldo_anterior),0.00) AS vl_cred_mes_ant
                                         , 0.00 AS vl_deb_mes
                                         , COALESCE(SUM(vl_lancamento),0.00) AS vl_cred_mes
                                         , 0.00 AS vl_deb_ate_mes
                                         , (COALESCE(SUM(saldo_anterior),0.00) + COALESCE(SUM(vl_lancamento),0.00)) AS vl_cred_ate_mes
                                         , 0.00 AS vl_deb_exercicio
                                         , 0.00 AS vl_cred_exercicio
                                    FROM contabilidade.fn_relatorio_razao ( '".$this->getDado('exercicio')."'
                                                                           , ''
                                                                           , '0.0.0.0.0.00.00.00.00.00'
                                                                           , '9.9.9.9.9.99.99.99.99.99'
                                                                           , '".$this->getDado('dt_inicial')."'
                                                                           , '".$this->getDado('dt_final')."'
                                                                           , '".$this->getDado('entidades')."'
                                                                           , '".$this->getDado('dt_inicial_ant')."'
                                                                           , '".$this->getDado('dt_final_ant')."'
                                                                           , 'N'
                                                                           , 'N'
                                                        ) AS retorno ( 
                                                                       cod_lote          INTEGER                                
                                                                      ,sequencia         INTEGER                                
                                                                      ,cod_historico     INTEGER                                
                                                                      ,nom_historico     VARCHAR                                
                                                                      ,complemento       VARCHAR                                
                                                                      ,observacao        TEXT                                   
                                                                      ,exercicio         CHAR(4)                                
                                                                      ,cod_entidade      INTEGER                                
                                                                      ,tipo              CHAR(1)                                
                                                                      ,vl_lancamento     NUMERIC                                
                                                                      ,tipo_valor        CHAR(1)                                
                                                                      ,dt_lote           VARCHAR                                
                                                                      ,dt_lote_formatado DATE                                
                                                                      ,cod_plano         INTEGER                                
                                                                      ,cod_estrutural    VARCHAR                                
                                                                      ,nom_conta         VARCHAR                                
                                                                      ,contra_partida    NUMERIC                                
                                                                      ,saldo_anterior    NUMERIC                                
                                                                      ,num_lancamentos   INTEGER      
                                                                    )
                                    WHERE tipo <> 'I'
                                      AND saldo_anterior <= 0
                                      AND tipo_valor = 'C'
                                    GROUP BY cod_estrutural

                                    -- DEBITO ANO ANTERIOR

                                    UNION ALL

                                    SELECT cod_estrutural
                                         , COALESCE(SUM(saldo_anterior),0.00) AS vl_deb_ant
                                         , 0.00 AS vl_cred_ant
                                         , 0.00 AS vl_deb_mes_ant
                                         , 0.00 AS vl_cred_mes_ant
                                         , 0.00 AS vl_deb_mes
                                         , 0.00 AS vl_cred_mes
                                         , 0.00 AS vl_deb_ate_mes
                                         , 0.00 AS vl_cred_ate_mes
                                         , COALESCE(SUM(vl_lancamento),0.00) AS vl_deb_exercicio
                                         , 0.00 AS vl_cred_exercicio
                                    FROM contabilidade.fn_relatorio_razao ( '".$this->getDado('exercicio')."'
                                                                           , ''
                                                                           , '0.0.0.0.0.00.00.00.00.00'
                                                                           , '9.9.9.9.9.99.99.99.99.99'
                                                                           , '01/01/".$this->getDado('exercicio')."'
                                                                           , '".$this->getDado('dt_final')."'
                                                                           , '".$this->getDado('entidades')."'
                                                                           , '01/01/".($this->getDado('exercicio')-1)."'
                                                                           , '31/12/".($this->getDado('exercicio')-1)."'
                                                                           , 'N'
                                                                           , 'N'
                                                                        ) AS retorno ( 
                                                                                       cod_lote          INTEGER                                
                                                                                      ,sequencia         INTEGER                                
                                                                                      ,cod_historico     INTEGER                                
                                                                                      ,nom_historico     VARCHAR                                
                                                                                      ,complemento       VARCHAR                                
                                                                                      ,observacao        TEXT                                   
                                                                                      ,exercicio         CHAR(4)                                
                                                                                      ,cod_entidade      INTEGER                                
                                                                                      ,tipo              CHAR(1)                                
                                                                                      ,vl_lancamento     NUMERIC                                
                                                                                      ,tipo_valor        CHAR(1)                                
                                                                                      ,dt_lote           VARCHAR                                
                                                                                      ,dt_lote_formatado DATE                                
                                                                                      ,cod_plano         INTEGER                                
                                                                                      ,cod_estrutural    VARCHAR                                
                                                                                      ,nom_conta         VARCHAR                                
                                                                                      ,contra_partida    NUMERIC                                
                                                                                      ,saldo_anterior    NUMERIC                                
                                                                                      ,num_lancamentos   INTEGER      
                                                                                    )
                                    WHERE tipo <> 'I'
                                      AND saldo_anterior > 0
                                      AND tipo_valor = 'D'
                                    GROUP BY cod_estrutural

                                    -- CREDITO ANO ANTERIOR

                                    UNION ALL

                                    SELECT cod_estrutural
                                         , 0.00 AS vl_deb_ant
                                         , COALESCE(SUM(saldo_anterior),0.00) AS vl_cred_ant
                                         , 0.00 AS vl_deb_mes_ant
                                         , 0.00 AS vl_cred_mes_ant
                                         , 0.00 AS vl_deb_mes
                                         , 0.00 AS vl_cred_mes
                                         , 0.00 AS vl_deb_ate_mes
                                         , 0.00 AS vl_cred_ate_mes
                                         , 0.00 AS vl_deb_exercicio
                                         , COALESCE(SUM(vl_lancamento),0.00) AS vl_cred_exercicio
                                    FROM contabilidade.fn_relatorio_razao ( '".$this->getDado('exercicio')."'
                                                                           , ''
                                                                           , '0.0.0.0.0.00.00.00.00.00'
                                                                           , '9.9.9.9.9.99.99.99.99.99'
                                                                           , '01/01/".$this->getDado('exercicio')."'
                                                                           , '".$this->getDado('dt_final')."'
                                                                           , '".$this->getDado('entidades')."'
                                                                           , '01/01/".($this->getDado('exercicio')-1)."'
                                                                           , '31/12/".($this->getDado('exercicio')-1)."'
                                                                           , 'N'
                                                                           , 'N'
                                                                        ) AS retorno ( 
                                                                                       cod_lote          INTEGER                                
                                                                                      ,sequencia         INTEGER                                
                                                                                      ,cod_historico     INTEGER                                
                                                                                      ,nom_historico     VARCHAR                                
                                                                                      ,complemento       VARCHAR                                
                                                                                      ,observacao        TEXT                                   
                                                                                      ,exercicio         CHAR(4)                                
                                                                                      ,cod_entidade      INTEGER                                
                                                                                      ,tipo              CHAR(1)                                
                                                                                      ,vl_lancamento     NUMERIC                                
                                                                                      ,tipo_valor        CHAR(1)                                
                                                                                      ,dt_lote           VARCHAR                                
                                                                                      ,dt_lote_formatado DATE                                
                                                                                      ,cod_plano         INTEGER                                
                                                                                      ,cod_estrutural    VARCHAR                                
                                                                                      ,nom_conta         VARCHAR                                
                                                                                      ,contra_partida    NUMERIC                                
                                                                                      ,saldo_anterior    NUMERIC                                
                                                                                      ,num_lancamentos   INTEGER      
                                                                                    )
                                    WHERE tipo <> 'I'
                                      AND saldo_anterior <= 0
                                      AND tipo_valor = 'C'
                                    GROUP BY cod_estrutural
                                ) AS retorno
                            GROUP BY cod_estrutural
                            ORDER BY cod_estrutural
                        ) AS tabela
                    ORDER BY tabela.conta_contabil
                ";
        return $stSql;
    }

}