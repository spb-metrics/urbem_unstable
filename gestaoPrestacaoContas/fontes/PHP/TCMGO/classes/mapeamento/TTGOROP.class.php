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

    * Data de Criação:

    * @author Analista: Gelson
    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOROP.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOROP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTGOROP()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    //Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
    public function montaRecuperaTodos()
    {
        $stSql = " select '10' as  tipo_registro
                , cons.codigo_obra
                , cons.num_orgao
                , cons.num_unidade
                , cons.ano_obra
                , cons.especificacao
                , sum ( saldo ) as saldo
            from (
               select   obra.ano_obra ||obra.cod_obra as codigo_obra
                    , obra.especificacao
                    , obra.ano_obra
                    , despesa.num_orgao
                    , despesa.num_unidade
                    , ( empenho.fn_consultar_valor_empenhado( empenho.exercicio ,empenho.cod_empenho ,empenho.cod_entidade ) -
                    empenho.fn_consultar_valor_empenhado_anulado( empenho.exercicio ,empenho.cod_empenho ,empenho.cod_entidade) )
                   -
                    ( empenho.fn_consultar_valor_liquidado(empenho.exercicio ,empenho.cod_empenho ,empenho.cod_entidade ) -
                      empenho.fn_consultar_valor_liquidado_anulado( empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade )
                    ) AS saldo
                 from tcmgo.obra
                 join tcmgo.obra_empenho
                   on ( obra.cod_obra = obra_empenho.cod_obra
                  and   obra.ano_obra = obra_empenho.ano_obra )
                 join empenho.empenho
                   on ( obra_empenho.cod_empenho  = empenho.cod_empenho
                  and   obra_empenho.cod_entidade = empenho.cod_entidade
                  and   obra_empenho.exercicio    = empenho.exercicio )
                 join empenho.pre_empenho
                   on ( empenho.exercicio       = pre_empenho.exercicio
                  and   empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho )
                 join empenho.pre_empenho_despesa
                   on ( pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                  and   pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho )
                 join orcamento.despesa
                   on ( despesa.exercicio   = pre_empenho_despesa.exercicio
                  and   despesa.cod_despesa = pre_empenho_despesa.cod_despesa )
                where obra_empenho.cod_entidade in ( ". $this->getDado ( 'stEntidades') ." )
                  and obra_empenho.exercicio = '". $this->getDado( 'exercicio' ) ."'
               ) as  cons
           group by tipo_registro
                  , cons.codigo_obra
                  , cons.num_orgao
                  , cons.num_unidade
                  , cons.ano_obra
                  , cons.especificacao";

        return $stSql;
    }
}
