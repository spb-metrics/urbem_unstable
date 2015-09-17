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
    * Extensão da Classe de mapeamento
    * Data de Criação: 14/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63115 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAEstorRec extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaDadosTribunal()
    {

        $stSql = " SELECT retorno.receita AS item_receita
                        , retorno.estornado AS vl_estorno
                        , receita.dt_criacao AS dt_receita
                        , TO_CHAR(MAX(estornada.timestamp_estornada),'dd/mm/yyyy') AS dt_estorno
                        , TO_CHAR(MAX(estornada.timestamp_estornada),'yyyymm') AS competencia
                        , REPLACE(conta_receita.cod_estrutural,'.','') AS conta_contabil
                        , 1 AS tipo_registro
                        , '".$this->getDado('unidade_gestora')."' AS unidade_gestora

                    FROM tesouraria.fn_relatorio_resumo_receita('".$this->getDado('entidades')."',           
                                                                '".$this->getDado('exercicio')."',                                           
                                                                '".$this->getDado('dt_inicial')."',                                           
                                                                '".$this->getDado('dt_final')."',                                           
                                                                '',                                           
                                                                 0 ,                                           
                                                                 0 ,                                           
                                                                 0 ,                                           
                                                                 0 ,                                           
                                                                 999999 ,                                           
                                                                'geral',                                           
                                                                '',                                           
                                                                '',                                            
                                                                'true' )                                        
                        AS retorno (
                                    receita      numeric,
                                    descricao    varchar,
                                    tipo         varchar,
                                    arrecadado   numeric,
                                    estornado    numeric,
                                    tipo_receita varchar
                                   )
              INNER JOIN orcamento.receita
                      ON receita.exercicio = '".$this->getDado('exercicio')."'
                     AND receita.cod_receita = retorno.receita

              INNER JOIN orcamento.conta_receita
                      ON conta_receita.cod_conta = receita.cod_conta
                     AND conta_receita.exercicio = receita.exercicio

               LEFT JOIN (
                            SELECT arrecadacao_receita.cod_receita
                                 , arrecadacao_receita.exercicio
                                 , CASE WHEN arrecadacao_estornada_receita.timestamp_estornada IS NULL
                                        THEN arrecadacao_receita.timestamp_arrecadacao
                                        ELSE arrecadacao_estornada_receita.timestamp_estornada
                                 END AS timestamp_estornada
                              FROM tesouraria.arrecadacao_receita
                        INNER JOIN tesouraria.arrecadacao_estornada_receita
                                ON arrecadacao_estornada_receita.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                               AND arrecadacao_estornada_receita.cod_receita = arrecadacao_receita.cod_receita
                               AND arrecadacao_estornada_receita.exercicio = arrecadacao_receita.exercicio
                               AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                             WHERE arrecadacao_receita.timestamp_arrecadacao = (SELECT MAX(ar.timestamp_arrecadacao)
                                                                                  FROM tesouraria.arrecadacao_receita AS ar
                                                                                 WHERE ar.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                                                                                   AND ar.cod_receita = arrecadacao_receita.cod_receita
                                                                                   AND ar.exercicio = arrecadacao_receita.exercicio
                                                                               )

                             UNION

                            SELECT arrecadacao_receita_dedutora.cod_receita_dedutora AS cod_receita
                                 , arrecadacao_receita_dedutora.exercicio
                                 , CASE WHEN arrecadacao_receita_dedutora_estornada.timestamp_estornada IS NULL
                                        THEN arrecadacao_receita_dedutora.timestamp_arrecadacao
                                        ELSE arrecadacao_receita_dedutora_estornada.timestamp_estornada
                                 END AS timestamp_estornada
                              FROM tesouraria.arrecadacao_receita_dedutora
                         LEFT JOIN tesouraria.arrecadacao_receita_dedutora_estornada
                                ON arrecadacao_receita_dedutora_estornada.cod_arrecadacao = arrecadacao_receita_dedutora.cod_arrecadacao
                               AND arrecadacao_receita_dedutora_estornada.cod_receita = arrecadacao_receita_dedutora.cod_receita
                               AND arrecadacao_receita_dedutora_estornada.exercicio = arrecadacao_receita_dedutora.exercicio
                               AND arrecadacao_receita_dedutora_estornada.timestamp_arrecadacao = arrecadacao_receita_dedutora.timestamp_arrecadacao
                               AND arrecadacao_receita_dedutora_estornada.cod_receita_dedutora = arrecadacao_receita_dedutora.cod_receita_dedutora
                             WHERE arrecadacao_receita_dedutora.timestamp_arrecadacao = (SELECT MAX(ard.timestamp_arrecadacao)
                                                                                           FROM tesouraria.arrecadacao_receita_dedutora AS ard
                                                                                          WHERE ard.cod_arrecadacao = arrecadacao_receita_dedutora.cod_arrecadacao
                                                                                            AND ard.cod_receita = arrecadacao_receita_dedutora.cod_receita
                                                                                            AND ard.exercicio = arrecadacao_receita_dedutora.exercicio
                                                                                            AND ard.cod_receita_dedutora = arrecadacao_receita_dedutora.cod_receita_dedutora
                                                                                        )

                             UNION

                            SELECT plano_analitica.cod_plano AS cod_receita
                                 , plano_analitica.exercicio
                                 , CASE WHEN transferencia_estornada.timestamp_estornada IS NULL
                                        THEN transferencia.timestamp_transferencia
                                        ELSE transferencia_estornada.timestamp_estornada
                                 END AS timestamp_estornada
                              FROM tesouraria.transferencia
                        INNER JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_plano = transferencia.cod_plano_credito
                               AND plano_analitica.exercicio = transferencia.exercicio
                        INNER JOIN tesouraria.transferencia_estornada
                                ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
                               AND transferencia_estornada.tipo = transferencia.tipo
                               AND transferencia_estornada.exercicio = transferencia.exercicio
                               AND transferencia_estornada.cod_lote = transferencia.cod_lote
                             WHERE transferencia.timestamp_transferencia = (SELECT MAX(tt.timestamp_transferencia)
                                                                              FROM tesouraria.transferencia AS tt
                                                                             WHERE tt.cod_lote = transferencia.cod_lote
                                                                               AND tt.cod_entidade = transferencia.cod_entidade
                                                                               AND tt.exercicio = transferencia.exercicio
                                                                               AND tt.tipo = transferencia.tipo
                                                                            )
                        ) AS estornada
                      ON estornada.cod_receita = receita.cod_receita
                     AND estornada.exercicio = receita.exercicio

                   WHERE retorno.estornado > 0.00

                GROUP BY retorno.receita, retorno.estornado, receita.dt_criacao, conta_receita.cod_estrutural

                ORDER BY item_receita
        ";
        return $stSql;
    }

}

?>