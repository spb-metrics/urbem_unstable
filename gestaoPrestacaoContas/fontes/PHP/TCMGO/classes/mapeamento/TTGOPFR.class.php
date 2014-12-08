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
    * Classe de mapeamento
    * Data de Criação: 07/05/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOPFR.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

class TTGOPFR extends Persistente
{
    public function TTGOPFR()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSQL = " select
            '10' as tipo_registro
            ,despesa.num_orgao
            ,case
                when despesa.exercicio <= '2001' then
                     lpad( despesa.num_unidade, 4, 0 ) ||
                     lpad( despesa.cod_funcao, 2, 0 )   ||
                     lpad( despesa.cod_programa, 2, 0 ) ||
                     lpad( '', 3, 0 ) ||
                     lpad( despesa.num_pao, 4, 0 ) ||
                     substr ( replace ( conta_despesa.cod_estrutural,'.','') , 0 , 7 )
                else
                     null
                end as dotacao2001
            ,case
                when despesa.exercicio > '2002' then
                     lpad( despesa.cod_programa, 4, 0 ) ||
                     lpad( despesa.num_unidade, 2, 0 ) ||
                     lpad( despesa.cod_funcao, 2, 0 )   ||
                     lpad( despesa.cod_subfuncao, 3, 0 ) ||
                     lpad( despesa.num_pao, 4, 0 ) ||
                     substr ( replace ( conta_despesa.cod_estrutural,'.','') , 0 , 7 ) ||
                     lpad( '', 2, 0 )
                else
                    null
                end as dotacao2002
            ,empenho.cod_empenho
            ,to_char(empenho.dt_empenho, 'dd/mm/yyyy') as dt_empenho
            ,sw_cgm.nom_cgm
            ,case
                when plano_conta.cod_estrutural like '3.4.6%' then
                    2
                else
                    1
             end as tipo_lancamento
            ,empenho.fn_consultar_valor_empenhado(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade) as vl_original
            ,case
                when despesa.exercicio = '".$this->getDado('exercicio')."' then
                    (empenho.fn_consultar_valor_empenhado(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade) -
                    empenho.fn_consultar_valor_empenhado_pago(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade))
                else
                    (empenho.fn_empenho_pago(despesa.exercicio,empenho.cod_empenho,empenho.cod_entidade,'01/01/'||despesa.exercicio,'31/12/'||despesa.exercicio) - empenho.fn_empenho_estornado(despesa.exercicio,empenho.cod_empenho,empenho.cod_entidade,'01/01/'||despesa.exercicio,'31/12/'||despesa.exercicio))
            end as vl_saldo_anterior
            ,case
                when despesa.exercicio = '".$this->getDado('exercicio')."' then
                    (empenho.fn_consultar_valor_empenhado(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade) -
                    empenho.fn_consultar_valor_empenhado_pago(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade))
                else
                    null
             end as vl_inscricao
            ,empenho.fn_consultar_valor_empenhado_pago(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade) as vl_baixa_pago
            ,empenho.fn_consultar_valor_empenhado_anulado(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade) as vl_cancelado
            ,'0.00' as vl_encampacao
            ,empenho.fn_empenho_pago(despesa.exercicio,empenho.cod_empenho,empenho.cod_entidade, '01/01/'||despesa.exercicio,'01/'||to_char(now(),'dd')||'/'||despesa.exercicio) as saldo_atual
            ,empenho.fn_empenho_liquidado(despesa.exercicio,empenho.cod_empenho,empenho.cod_entidade, '01/01/'||despesa.exercicio,'01/'||to_char(now(),'dd')||'/'||despesa.exercicio) - empenho.fn_empenho_estornado(despesa.exercicio,empenho.cod_empenho,empenho.cod_entidade, '01/01/'||despesa.exercicio,'01/'||to_char(now(),'dd')||'/'||despesa.exercicio) as vl_processado
            ,empenho.fn_consultar_valor_empenhado(despesa.exercicio, empenho.cod_empenho, empenho.cod_entidade) - (empenho.fn_empenho_liquidado(despesa.exercicio,empenho.cod_empenho,empenho.cod_entidade, '01/01/'||despesa.exercicio,'01/'||to_char(now(),'dd')||'/'||despesa.exercicio) +  empenho.fn_empenho_estornado(despesa.exercicio,empenho.cod_empenho,empenho.cod_entidade, '01/01/'||despesa.exercicio,'01/'||to_char(now(),'dd')||'/'||despesa.exercicio)) as vl_n_processado
        from
             orcamento.despesa
            ,orcamento.conta_despesa
            ,empenho.empenho
            ,empenho.pre_empenho
            ,empenho.pre_empenho_despesa
            ,contabilidade.plano_conta
            ,sw_cgm
        where
                empenho.exercicio = pre_empenho.exercicio
            and empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

            and pre_empenho.exercicio = pre_empenho_despesa.exercicio
            and pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

            and pre_empenho_despesa.cod_despesa = despesa.cod_despesa
            and pre_empenho_despesa.exercicio = despesa.exercicio

            and despesa.exercicio = conta_despesa.exercicio
            and despesa.cod_conta = conta_despesa.cod_conta

            and plano_conta.exercicio = conta_despesa.exercicio
            and '3.'||conta_despesa.cod_estrutural like publico.fn_mascarareduzida(plano_conta.cod_estrutural)||'%'

            and pre_empenho.cgm_beneficiario = sw_cgm.numcgm

            and despesa.cod_entidade in ( ".$this->getDado('stEntidades')." )

            and despesa.exercicio <= '".$this->getDado('exercicio')."'

        group by
             tipo_registro
            ,num_orgao
            ,dotacao2001
            ,dotacao2002
            ,dt_empenho
            ,despesa.exercicio
            ,empenho.cod_entidade
            ,empenho.cod_empenho
            ,nom_cgm
            ,tipo_lancamento
            ,vl_original
            ,vl_baixa_pago
            ,vl_saldo_anterior
            ,vl_inscricao
            ,vl_cancelado
            ,saldo_atual
            ,vl_processado
            ,vl_n_processado
    ";

        return $stSQL;
    }
}
