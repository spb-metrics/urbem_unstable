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

    * Data de Criação   : 16/05/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    * $Id: TTGOBLP.class.php 61563 2015-02-05 15:54:03Z lisiane $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOBLP extends Persistente
{

    public function TTGOBLP()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );

    }

    public function montaRecuperaTodos()
    {
        $exercicio = "'".$this->getDado('exercicio')."'";
        $dtInicial = "'01/01/".$this->getDado('exercicio')."'";
        $dtFinal   = "'31/12/".$this->getDado('exercicio')."'";
        $entidade  = "'".$this->getDado('cod_entidade')."'";
        $stSql = "
                SELECT *
                  FROM ( 
                        SELECT 10 AS registro
                             , '01' AS tipo_lancamento
                             , tipo_conta
                             , SUM(vl_saldo_anterior) AS vl_saldo_anterior
                             , SUM(vl_saldo_creditos) AS vl_saldo_creditos
                             , SUM(vl_saldo_debitos) AS vl_saldo_debitos
                             , SUM(vl_saldo_atual) AS vl_saldo_atual  
                             , SUM((vl_saldo_anterior + vl_saldo_creditos) - vl_saldo_debitos) AS saldo_exerc_seguinte  
                        FROM( SELECT * 
                                FROM tcmgo.balanco_patrimonial_ativo( ".$exercicio.",".$dtInicial.",".$dtFinal.",".$entidade." )
                                  AS ( cod_estrutural VARCHAR
                                     , nivel INTEGER
                                     , nom_conta VARCHAR
                                     , vl_saldo_anterior NUMERIC
                                     , vl_saldo_debitos NUMERIC
                                     , vl_saldo_creditos NUMERIC
                                     , vl_saldo_atual NUMERIC 
                                     , tipo_conta INTEGER )
                              ORDER BY cod_estrutural ) AS TABELA
                              GROUP BY tipo_conta

               UNION ALL

                    SELECT 10 AS registro
                         , '02' AS tipo_lancamento
                         , tipo_conta
                         , SUM(vl_saldo_anterior) AS vl_saldo_anterior
                         , SUM(vl_saldo_creditos) AS vl_saldo_creditos
                         , SUM(vl_saldo_debitos) AS vl_saldo_debitos
                         , SUM(vl_saldo_atual) AS vl_saldo_atual  
                         , SUM((vl_saldo_anterior + vl_saldo_creditos) - vl_saldo_debitos) AS saldo_exerc_seguinte
                     FROM( SELECT * 
                             FROM tcmgo.balanco_patrimonial_passivo( ".$exercicio.",".$dtInicial.",".$dtFinal.",".$entidade." ) 
                               AS ( cod_estrutural VARCHAR
                                  , nivel INTEGER
                                  , nom_conta VARCHAR
                                  , vl_saldo_anterior NUMERIC
                                  , vl_saldo_debitos NUMERIC
                                  , vl_saldo_creditos NUMERIC
                                  , vl_saldo_atual NUMERIC 
                                  , tipo_conta INTEGER )
                           ORDER BY cod_estrutural ) AS TABELA
                           GROUP BY tipo_conta

              UNION ALL

                SELECT 10 AS registro
                      ,	'02' AS tipo_lancamento
                      , tipo_conta
                      , SUM(vl_saldo_anterior) AS vl_saldo_anterior
                      , SUM(vl_saldo_creditos) AS vl_saldo_creditos
                      , SUM(vl_saldo_debitos) AS vl_saldo_debitos
                      , SUM(vl_saldo_atual) AS vl_saldo_atual  
                      , SUM((vl_saldo_anterior + vl_saldo_creditos) - vl_saldo_debitos) AS saldo_exerc_seguinte
                FROM(  
                SELECT * 
                  FROM tcmgo.balanco_patrimonial_patrimonio_liquido( ".$exercicio.",".$dtInicial.",".$dtFinal.",".$entidade." ) 
                    AS ( cod_estrutural VARCHAR
                       , nivel INTEGER
                       , nom_conta VARCHAR
                       , vl_saldo_anterior NUMERIC
                       , vl_saldo_debitos NUMERIC
                       , vl_saldo_creditos NUMERIC
                       , vl_saldo_atual NUMERIC 
                       , tipo_conta INTEGER)
                ORDER BY cod_estrutural ) AS TABELA
                GROUP BY tipo_conta
           ) AS tabela
     ORDER BY tipo_lancamento, tipo_conta ";

    return $stSql;

    }
}
