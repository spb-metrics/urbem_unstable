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
    public function __construct()
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
                        , LPAD(".$this->getDado('unidade_gestora')."::VARCHAR,4,'0') AS unidade_gestora
                        , ".$this->getDado('exercicio')."::VARCHAR||LPAD(".$this->getDado('mes')."::VARCHAR,2,'0') AS competencia
                        , '' AS reservado_tcm
                        , cod_estrutural AS conta_contabil
                        , SUM(deb_ant) AS deb_ant
                        , SUM(cred_ant) AS cred_ant
                        , SUM(deb_mes) AS deb_mes
                        , SUM(cred_mes) AS cred_mes
                        , SUM(deb_mes_ant) AS deb_mes_ant
                        , SUM(cred_mes_ant) AS cred_mes_ant
                        , (SUM(COALESCE(deb_mes,0.00))+SUM(COALESCE(deb_mes_ant,0.00))) AS deb_ate_mes
                        , (SUM(COALESCE(cred_mes,0.00))+SUM(COALESCE(cred_mes_ant,0.00))) AS cred_ate_mes
                        , (SUM(COALESCE(deb_mes,0.00))+SUM(COALESCE(deb_mes_ant,0.00))) AS deb_exercicio
                        , (SUM(COALESCE(cred_mes,0.00))+SUM(COALESCE(cred_mes_ant,0.00))) AS cred_exercicio

                    FROM
                    (
                      SELECT
                            plano_conta.cod_estrutural
                            ,0.00 AS deb_ant
                            ,0.00 AS cred_ant
                            ,COALESCE(SUM(retorno_mes.saldo_debitos),0.00) AS deb_mes
                            ,COALESCE(SUM(retorno_mes.saldo_creditos),0.00) AS cred_mes
                            ,0.00 AS deb_mes_ant
                            ,0.00 AS cred_mes_ant
                      FROM
                          contabilidade.plano_conta
                      
                      INNER JOIN
                        (
                          SELECT *
                          FROM
                          contabilidade.fn_rl_balancete_verificacao('".$this->getDado('exercicio')."', ' cod_entidade IN (".$this->getDado('entidades').") ', '".$this->getDado('dt_inicial')."', '".$this->getDado('dt_final')."', 'S') AS retorno
                          (
                          cod_estrutural VARCHAR
                          ,nivel INTEGER
                          ,nom_conta VARCHAR
                          ,cod_sistema INTEGER
                          ,indicador_superavit CHAR(12)
                          ,saldo_anterior NUMERIC
                          ,saldo_debitos NUMERIC
                          ,saldo_creditos NUMERIC
                          ,saldo_atual NUMERIC
                          )
                        ) AS retorno_mes
                       ON plano_conta.cod_estrutural = retorno_mes.cod_estrutural
                      AND plano_conta.exercicio = '".$this->getDado('exercicio')."'
                      AND plano_conta.indicador_superavit = retorno_mes.indicador_superavit
                      
                      GROUP BY plano_conta.cod_estrutural
                      
                      UNION ALL
                      
                      SELECT
                            plano_conta.cod_estrutural
                            ,0.00 AS deb_ant
                            ,0.00 AS cred_ant
                            ,0.00 AS deb_mes
                            ,0.00 AS cred_mes
                            ,COALESCE(SUM(retorno_mes_ant.saldo_debitos),0.00) AS deb_mes_ant
                            ,COALESCE(SUM(retorno_mes_ant.saldo_creditos),0.00) AS cred_mes_ant
                      FROM
                          contabilidade.plano_conta
                      
                      INNER JOIN
                        (
                          SELECT
                          
                          *
                          
                          FROM
                          
                          contabilidade.fn_rl_balancete_verificacao('".$this->getDado('exercicio')."', ' cod_entidade IN (".$this->getDado('entidades').") ', '".$this->getDado('dt_inicial_ant')."', '".$this->getDado('dt_final_ant')."', 'A') AS retorno
                          (
                          cod_estrutural VARCHAR
                          ,nivel INTEGER
                          ,nom_conta VARCHAR
                          ,cod_sistema INTEGER
                          ,indicador_superavit CHAR(12)
                          ,saldo_anterior NUMERIC
                          ,saldo_debitos NUMERIC
                          ,saldo_creditos NUMERIC
                          ,saldo_atual NUMERIC
                          )
                        ) AS retorno_mes_ant
                       ON plano_conta.cod_estrutural = retorno_mes_ant.cod_estrutural
                      AND plano_conta.exercicio = '".$this->getDado('exercicio')."'
                      AND plano_conta.indicador_superavit = retorno_mes_ant.indicador_superavit
                      
                      GROUP BY plano_conta.cod_estrutural
                      
                      UNION ALL
                      
                      SELECT
                            plano_conta.cod_estrutural
                            ,COALESCE(SUM(retorno_anterior.saldo_debitos),0.00) AS deb_ant
                            ,COALESCE(SUM(retorno_anterior.saldo_creditos),0.00) AS cred_ant
                            ,0.00 AS deb_mes
                            ,0.00 AS cred_mes
                            ,0.00 AS deb_mes_ant
                            ,0.00 AS cred_mes_ant
                      FROM
                          contabilidade.plano_conta
                      
                      INNER JOIN
                        (
                          SELECT
                          
                          *
                          
                          FROM
                          
                          contabilidade.fn_rl_balancete_verificacao('".$this->getDado('exercicio_ant')."', ' cod_entidade IN (".$this->getDado('entidades').") ', '01/01/".$this->getDado('exercicio_ant')."', '31/12/".$this->getDado('exercicio_ant')."', 'A') AS retorno
                          (
                          cod_estrutural VARCHAR
                          ,nivel INTEGER
                          ,nom_conta VARCHAR
                          ,cod_sistema INTEGER
                          ,indicador_superavit CHAR(12)
                          ,saldo_anterior NUMERIC
                          ,saldo_debitos NUMERIC
                          ,saldo_creditos NUMERIC
                          ,saldo_atual NUMERIC
                          )
                        ) AS retorno_anterior
                       ON plano_conta.cod_estrutural = retorno_anterior.cod_estrutural
                      AND plano_conta.exercicio = '".$this->getDado('exercicio')."'
                      AND plano_conta.indicador_superavit = retorno_anterior.indicador_superavit
                      
                      GROUP BY plano_conta.cod_estrutural
                      
                    ) AS retorno
                      
                    GROUP BY cod_estrutural
                      
                    ORDER BY cod_estrutural
                ";
        return $stSql;
    }

}